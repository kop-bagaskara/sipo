<?php

namespace App\Services;

use App\Models\ApprovalSetting;
use App\Models\SplRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk handle approval workflow SPL
 * Mirip dengan ApprovalService untuk EmployeeRequest, tapi khusus untuk SplRequest
 */
class SplApprovalService
{
    /**
     * Get approval chain untuk SPL
     * PENTING:
     * 1. Ambil URUTAN dari ApprovalSetting (tb_approval_hr_settings) berdasarkan approval_order
     * 2. TIDAK perlu cek DivisiApprovalSetting untuk skip level (semua level di ApprovalSetting digunakan)
     * 3. Ambil user approver berdasarkan role_key dari ApprovalSetting
     */
    public function getApprovalChain(SplRequest $splRequest)
    {
        // Get division from supervisor
        $divisiId = $splRequest->divisi_id;

        if (!$divisiId) {
            throw new \Exception('SPL tidak memiliki divisi');
        }

        // HARDCODE: PREPRESS (3) menggunakan alur yang sama dengan PRODUKSI (4)
        if ($divisiId === 3) {
            $divisiId = 4;
        }

        // 1. Ambil flow dari ApprovalSetting berdasarkan request_type = 'spl'
        $approvalFlow = ApprovalSetting::getApprovalFlow('spl', $divisiId);

        if ($approvalFlow->isEmpty()) {
            throw new \Exception('Setting approval untuk SPL belum dikonfigurasi');
        }

        // 2. Batch query untuk semua user approvers sekaligus
        $userQueries = [];
        $userBasedIds = [];

        foreach ($approvalFlow as $setting) {
            $roleKey = $setting->role_key;
            // dd($roleKey);

            if ($roleKey === 'spv_division') {
                $userQueries[] = ['divisi' => $splRequest->divisi_id, 'jabatan' => 5, 'role' => 'spv_division'];
            } elseif ($roleKey === 'head_division') {
                // Ambil allowed_jabatan dari setting (bisa [3, 4] untuk MANAGER dan HEAD)
                $allowedJabatan = $setting->allowed_jabatan ?? [4]; // Default hanya HEAD jika tidak ada

                // EXCEPTION: Untuk SPL dari Prepress (divisi 3), HEAD PRODUKSI (divisi 4) juga bisa approve
                $divisiForHead = $splRequest->divisi_id;
                if ($splRequest->divisi_id == 3) {
                    // Prepress: cari HEAD/MANAGER di divisi 3 dan 4 berdasarkan allowed_jabatan
                    foreach ($allowedJabatan as $jabatan) {
                        $userQueries[] = ['divisi' => 3, 'jabatan' => $jabatan, 'role' => 'head_division'];
                        $userQueries[] = ['divisi' => 4, 'jabatan' => $jabatan, 'role' => 'head_division'];
                    }
                } else {
                    // Untuk divisi lain, ambil berdasarkan allowed_jabatan
                    foreach ($allowedJabatan as $jabatan) {
                        $userQueries[] = ['divisi' => $divisiForHead, 'jabatan' => $jabatan, 'role' => 'head_division'];
                    }
                }
            } elseif ($roleKey === 'manager') {
                $userQueries[] = ['divisi' => $splRequest->divisi_id, 'jabatan' => 3, 'role' => 'manager'];
            } elseif ($roleKey === 'hr') {
                $userQueries[] = ['divisi' => 7, 'jabatan' => null, 'role' => 'hr'];
            } elseif ($setting->approver_type === 'user' && $setting->user_id) {
                $userBasedIds[] = $setting->user_id;
            }
        }

        // Batch query: ambil semua user sekaligus
        $allUsers = collect();
        $userQueriesByKey = [];
        foreach ($userQueries as $query) {
            $key = $query['role'] . '_' . ($query['divisi'] ?? '') . '_' . ($query['jabatan'] ?? '');
            if (!isset($userQueriesByKey[$key])) {
                $userQueriesByKey[$key] = $query;
            }
        }

        foreach ($userQueriesByKey as $query) {
            $q = User::on('pgsql')->where('jabatan', '!=', 7); // Kecualikan KARYAWAN
            if ($query['divisi']) {
                $q->where('divisi', $query['divisi']);
            }
            if ($query['jabatan']) {
                $q->where('jabatan', $query['jabatan']);
            }
            $allUsers = $allUsers->merge($q->get());
        }

        // Query user-based approvers
        if (!empty($userBasedIds)) {
            $userBasedUsers = User::on('pgsql')->whereIn('id', $userBasedIds)
                ->where('jabatan', '!=', 7)
                ->get();
            $allUsers = $allUsers->merge($userBasedUsers);
        }

        // Index users by role, divisi, jabatan untuk akses cepat
        // PENTING: MANAGER (jabatan 3) bisa masuk ke head_division jika allowed_jabatan mencakup 3
        $usersByRole = [];

        // Ambil head_division setting untuk cek allowed_jabatan
        $headDivisionSetting = $approvalFlow->firstWhere('role_key', 'head_division');
        $headDivisionAllowedJabatan = $headDivisionSetting->allowed_jabatan ?? [4];

        foreach ($allUsers as $user) {
            $role = null;
            if ($user->jabatan == 5 && $user->divisi == $splRequest->divisi_id) {
                $role = 'spv_division';
            } elseif ($user->jabatan == 4) {
                // HEAD: EXCEPTION untuk Prepress (divisi 3), HEAD PRODUKSI (divisi 4) juga bisa approve
                if ($splRequest->divisi_id == 3 && ($user->divisi == 3 || $user->divisi == 4)) {
                    $role = 'head_division';
                } elseif ($user->divisi == $splRequest->divisi_id) {
                    $role = 'head_division';
                }
            } elseif ($user->jabatan == 3 && $user->divisi == $splRequest->divisi_id) {
                // MANAGER: Cek apakah ada setting head_division dengan allowed_jabatan yang mencakup 3
                if ($headDivisionSetting &&
                    !empty($headDivisionAllowedJabatan) &&
                    in_array(3, $headDivisionAllowedJabatan)) {
                    // MANAGER masuk ke head_division jika allowed_jabatan mencakup 3
                    $role = 'head_division';
                } else {
                    // Jika tidak, MANAGER masuk ke role manager (jika ada setting manager terpisah)
                    $role = 'manager';
                }
            } elseif ($user->divisi == 7) {
                $role = 'hr';
            }

            if ($role) {
                if (!isset($usersByRole[$role])) {
                    $usersByRole[$role] = collect();
                }
                $usersByRole[$role]->push($user);
            }
        }

        // Index user-based approvers by user_id
        $usersById = [];
        foreach ($allUsers as $user) {
            $usersById[$user->id] = $user;
        }

        // 3. Build chain dengan URUTAN mengikuti approval_order dari ApprovalSetting
        $chain = [];

        foreach ($approvalFlow as $setting) {
            $roleKey = $setting->role_key;
            $approvalOrder = $setting->approval_order;
            $levelName = $setting->approval_level ?? $roleKey;
            $users = collect();

            // Ambil user approver dari pre-loaded users
            if ($roleKey === 'spv_division') {
                $users = $usersByRole['spv_division'] ?? collect();
            } elseif ($roleKey === 'head_division') {
                $users = $usersByRole['head_division'] ?? collect();
            } elseif ($roleKey === 'manager') {
                $users = $usersByRole['manager'] ?? collect();
            } elseif ($roleKey === 'hr') {
                $users = $usersByRole['hr'] ?? collect();
            } elseif ($setting->approver_type === 'user' && $setting->user_id) {
                if (isset($usersById[$setting->user_id])) {
                    $users = collect([$usersById[$setting->user_id]]);
                } else {
                    $users = collect();
                }
            } else {
                continue;
            }

            // Tambahkan ke chain jika ada user
            if ($users->isNotEmpty()) {
                $chainKey = 'order_' . $approvalOrder;
                $chain[$chainKey] = [
                    'users' => $users,
                    'user_id' => $users->first()->id,
                    'user' => $users->first(),
                    'level_name' => $levelName,
                    'approval_order' => $approvalOrder,
                    'role_key' => $roleKey,
                    'setting_id' => $setting->id,
                    'user_count' => $users->count()
                ];
            }
        }

        // Sort by approval_order
        uasort($chain, function ($a, $b) {
            return $a['approval_order'] <=> $b['approval_order'];
        });

        return $chain;
    }

    /**
     * Update request status based on approvals
     */
    public function updateRequestStatus(SplRequest $splRequest)
    {
        $chain = $this->getApprovalChain($splRequest);
        $hasRejected = false;
        $allApproved = true;
        $currentOrder = 0;

        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;
            $isApproved = false;
            $isRejected = false;

            // Cek status berdasarkan role_key
            if ($roleKey === 'spv_division') {
                // SPV menggunakan supervisor_id (yang membuat SPL)
                // Untuk SPL, SPV yang membuat sudah otomatis approve, jadi skip
                $isApproved = true; // SPV yang membuat sudah approve
            } elseif ($roleKey === 'head_division') {
                // head_division bisa di-approve oleh HEAD atau MANAGER
                $isApproved = !is_null($splRequest->head_approved_at) || !is_null($splRequest->manager_approved_at);
                $isRejected = !is_null($splRequest->head_rejected_at) || !is_null($splRequest->manager_rejected_at);
            } elseif ($roleKey === 'manager') {
                $isApproved = !is_null($splRequest->manager_approved_at);
                $isRejected = !is_null($splRequest->manager_rejected_at);
            } elseif ($roleKey === 'hr') {
                $isApproved = !is_null($splRequest->hrd_approved_at);
                $isRejected = !is_null($splRequest->hrd_rejected_at);
            }

            if ($isRejected) {
                $hasRejected = true;
                break;
            }

            if ($isApproved) {
                $currentOrder = $approverData['approval_order'] ?? 0;
            }

            if (!$isApproved && $roleKey !== 'spv_division') {
                $allApproved = false;
            }
        }

        // Prepare update data
        $updateData = [];
        $updateData['current_approval_order'] = $currentOrder;

        // Update overall status
        if ($hasRejected) {
            $updateData['status'] = SplRequest::STATUS_REJECTED;
        } elseif ($allApproved) {
            $updateData['status'] = SplRequest::STATUS_HR_APPROVED;
        } else {
            // Check specific statuses
            if (!is_null($splRequest->hrd_approved_at)) {
                $updateData['status'] = SplRequest::STATUS_HR_APPROVED;
            } elseif (!is_null($splRequest->manager_approved_at)) {
                $updateData['status'] = SplRequest::STATUS_MANAGER_APPROVED;
            } elseif (!is_null($splRequest->head_approved_at)) {
                $updateData['status'] = SplRequest::STATUS_HEAD_APPROVED;
            } else {
                $updateData['status'] = SplRequest::STATUS_PENDING;
            }
        }

        $splRequest->update($updateData);
    }

    /**
     * Get pending SPL requests for a user
     */
    public static function getPendingRequestsForUser($user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        $userObj = $user instanceof User ? $user : User::find($userId);

        if (!$userObj) {
            return collect();
        }

        // Filter berdasarkan divisi user
        // EXCEPTION: HEAD PRODUKSI (divisi 4) juga melihat SPL dari Prepress (divisi 3)
        $divisiIds = [$userObj->divisi];
        if ((int) $userObj->jabatan === 4 && (int) $userObj->divisi === 4) {
            // HEAD PRODUKSI juga melihat dari Prepress
            $divisiIds[] = 3;
        }

        // Ambil SPL dengan status pending dan divisi yang sesuai
        // Eager load relationships untuk performa lebih baik
        $query = SplRequest::with([
            'supervisor',
            'supervisor.divisiUser',
            'divisi',
            'employees',
            'head',
            'manager',
            'hrd'
        ])->whereIn('status', [
            SplRequest::STATUS_PENDING,
            SplRequest::STATUS_HEAD_APPROVED,
            SplRequest::STATUS_MANAGER_APPROVED,
        ])->where('status', '!=', SplRequest::STATUS_REJECTED)
        ->whereIn('divisi_id', $divisiIds);

        $allPendingRequests = $query->get();

        Log::info('SPL Pending Debug', [
            'user_id' => $userId,
            'user_jabatan' => $userObj->jabatan,
            'user_divisi' => $userObj->divisi,
            'divisi_ids' => $divisiIds,
            'total_found' => $allPendingRequests->count(),
            'spl_ids' => $allPendingRequests->pluck('id')->toArray(),
        ]);

        $pendingForUser = collect();

        $approvalService = new self();

        foreach ($allPendingRequests as $splRequest) {
            try {
                $chain = $approvalService->getApprovalChain($splRequest);
                // dd($chain);

                Log::info('SPL Chain Debug', [
                    'spl_id' => $splRequest->id,
                    'spl_number' => $splRequest->spl_number,
                    'spl_divisi_id' => $splRequest->divisi_id,
                    'chain_count' => count($chain),
                    'chain_keys' => array_keys($chain),
                ]);

                foreach ($chain as $level => $approverData) {
                    $users = $approverData['users'] ?? collect();
                    $roleKeyInChain = $approverData['role_key'] ?? null;

                    // dd($roleKeyInChain);

                    $userInLevel = $users->contains('id', $userId);

                    Log::debug('SPL Approval Level Check', [
                        'spl_id' => $splRequest->id,
                        'level' => $level,
                        'role_key' => $roleKeyInChain,
                        'user_id' => $userId,
                        'users_in_level' => $users->pluck('id')->toArray(),
                        'user_in_level' => $userInLevel,
                    ]);

                    if ($userInLevel) {
                        $isPending = false;

                        if ($roleKeyInChain === 'spv_division') {
                            // SPV yang membuat sudah otomatis approve
                            continue;
                        } elseif ($roleKeyInChain === 'head_division') {
                            // head_division bisa di-approve oleh HEAD atau MANAGER
                            // Cek berdasarkan jabatan user
                            if ((int) $userObj->jabatan === 3) {
                                // MANAGER cek manager_approved_at
                                $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                            } else {
                                // HEAD cek head_approved_at
                                $isPending = is_null($splRequest->head_approved_at) && is_null($splRequest->head_rejected_at);
                            }
                        } elseif ($roleKeyInChain === 'manager') {
                            $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                        } elseif ($roleKeyInChain === 'hr') {
                            $isPending = is_null($splRequest->hrd_approved_at) && is_null($splRequest->hrd_rejected_at);
                        }

                        // Cek apakah level sebelumnya sudah di-approve
                        $previousLevelsApproved = true;
                        foreach ($chain as $prevLevel => $prevApproverData) {
                            if ($prevLevel === $level) {
                                break;
                            }

                            $prevRoleKey = $prevApproverData['role_key'] ?? null;
                            $prevApproved = false;

                            if ($prevRoleKey === 'spv_division') {
                                $prevApproved = true; // SPV yang membuat sudah approve
                            } elseif ($prevRoleKey === 'head_division') {
                                // head_division bisa di-approve oleh HEAD atau MANAGER
                                // Cek apakah HEAD atau MANAGER sudah approve
                                $prevApproved = !is_null($splRequest->head_approved_at) || !is_null($splRequest->manager_approved_at);
                            } elseif ($prevRoleKey === 'manager') {
                                $prevApproved = !is_null($splRequest->manager_approved_at);
                            } elseif ($prevRoleKey === 'hr') {
                                $prevApproved = !is_null($splRequest->hrd_approved_at);
                            }

                            if (!$prevApproved) {
                                $previousLevelsApproved = false;
                                break;
                            }
                        }

                        if ($isPending && $previousLevelsApproved) {
                            Log::info('SPL Added to Pending', [
                                'spl_id' => $splRequest->id,
                                'spl_number' => $splRequest->spl_number,
                                'role_key' => $roleKeyInChain,
                                'is_pending' => $isPending,
                                'previous_approved' => $previousLevelsApproved,
                            ]);
                            $pendingForUser->push($splRequest);
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting pending SPL for user', [
                    'spl_id' => $splRequest->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                continue;
            }
        }

        Log::info('SPL Pending Result', [
            'user_id' => $userId,
            'total_pending' => $pendingForUser->count(),
            'pending_ids' => $pendingForUser->pluck('id')->toArray(),
        ]);

        return $pendingForUser->unique('id')->values();
    }
}

