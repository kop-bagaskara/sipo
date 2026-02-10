<?php

namespace App\Http\Controllers;

use App\Models\ApprovalSetting;
use App\Models\DivisiApprovalSetting;
use App\Models\EmployeeRequest;
use App\Models\OvertimeEmployee;
use App\Models\HrNotification;
use App\Models\User;
use App\Models\VehicleAssetRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HRApprovalController extends Controller
{
    /**
     * Debug method untuk mengecek permohonan tertentu
     * Untuk absence: cek tb_approval_hr_settings (urutan) dan tb_divisi_approval_settings (enabled/disabled)
     */
    public function debugRequest($id)
    {
        $request = EmployeeRequest::with(['employee', 'supervisor', 'head', 'manager', 'hr'])->findOrFail($id);

        // Get employee divisi
        $divisi = ($request->request_type === 'absence' && $request->employee) ? $request->employee->divisi : null;

        // Untuk ABSENCE: ambil dari tb_approval_hr_settings (semua level, termasuk yang mungkin disabled)
        // Ini adalah approval flow GLOBAL yang menentukan URUTAN
        $approvalFlowGlobal = null;
        if ($request->request_type === 'absence' && $divisi) {
            // Untuk absence, getApprovalFlow sudah mempertimbangkan DivisiApprovalSetting
            // Tapi kita perlu ambil juga yang global untuk melihat semua level
            $approvalFlowGlobal = \App\Models\ApprovalSetting::active()
                ->forRequestType('absence')
                ->orderBy('approval_order')
                ->get();
        } else {
            $approvalFlowGlobal = \App\Models\ApprovalSetting::active()
                ->forRequestType($request->request_type)
                ->orderBy('approval_order')
                ->get();
        }

        // Get approval flow (sudah mempertimbangkan DivisiApprovalSetting untuk absence)
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $divisi);

        // Get divisi approval setting (untuk absence)
        $divisiSetting = null;
        if ($request->request_type === 'absence' && $divisi) {
            $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $divisi)
                ->where('is_active', true)
                ->first();
        }

        // Get approval chain (sudah di-filter berdasarkan DivisiApprovalSetting)
        $approvalService = new ApprovalService();
        $approvalChain = $approvalService->getApprovalChain($request);

        // Check who should approve first berdasarkan approval chain yang sebenarnya
        $currentOrder = $request->current_approval_order ?? 0;
        $nextApprover = null;
        $nextApproverFromChain = null;

        // Cek dari approval flow (urutan)
        foreach ($approvalFlow as $setting) {
            if ($setting->approval_order > $currentOrder) {
                $nextApprover = $setting;
                break;
            }
        }

        // Cek dari approval chain (yang sebenarnya enabled)
        foreach ($approvalChain as $chainKey => $chainData) {
            $chainOrder = $chainData['approval_order'] ?? 0;
            if ($chainOrder > $currentOrder) {
                $nextApproverFromChain = $chainData;
                break;
            }
        }

        // Check if should appear in SPV pending
        // Untuk absence, perlu cek: SPV ada di flow DAN enabled di divisi setting
        $shouldAppearInSpvPending = false;
        if ($request->status === EmployeeRequest::STATUS_PENDING) {
            if ($request->request_type === 'absence' && $divisiSetting) {
                // Untuk absence: cek apakah SPV enabled di DivisiApprovalSetting
                $spvEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                if ($spvEnabled) {
                    $spvSetting = $approvalFlowGlobal->firstWhere('role_key', 'spv_division');
                    if ($spvSetting) {
                        $spvOrder = $spvSetting->approval_order;
                        if ($spvOrder == 1) {
                            // SPV is first, check if not approved yet
                            $shouldAppearInSpvPending = is_null($request->supervisor_approved_at) && is_null($request->supervisor_rejected_at);
                        } else {
                            // SPV is not first, check if previous levels approved
                            $allPreviousApproved = true;
                            for ($i = 1; $i < $spvOrder; $i++) {
                                $prev = $approvalFlowGlobal->firstWhere('approval_order', $i);
                                if (!$prev) continue;

                                // Cek apakah level sebelumnya enabled
                                $prevEnabled = false;
                                if ($prev->role_key === 'head_division') {
                                    $prevEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                    if ($prevEnabled && (is_null($request->head_approved_at) || !is_null($request->head_rejected_at))) {
                                        $allPreviousApproved = false;
                                        break;
                                    }
                                } elseif ($prev->role_key === 'manager') {
                                    $prevEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                    if ($prevEnabled && (is_null($request->manager_approved_at) || !is_null($request->manager_rejected_at))) {
                                        $allPreviousApproved = false;
                                        break;
                                    }
                                }
                            }
                            $shouldAppearInSpvPending = $allPreviousApproved
                                && is_null($request->supervisor_approved_at)
                                && is_null($request->supervisor_rejected_at);
                        }
                    }
                }
            } else {
                // Untuk shift_change atau request type lain
                $spvSetting = $approvalFlow->firstWhere('role_key', 'spv_division');
                if ($spvSetting) {
                    $spvOrder = $spvSetting->approval_order;
                    if ($spvOrder == 1) {
                        $shouldAppearInSpvPending = is_null($request->supervisor_approved_at) && is_null($request->supervisor_rejected_at);
                    } else {
                        $allPreviousApproved = true;
                        for ($i = 1; $i < $spvOrder; $i++) {
                            $prev = $approvalFlow->firstWhere('approval_order', $i);
                            if (!$prev) continue;

                            if ($prev->role_key === 'head_division') {
                                if (is_null($request->head_approved_at) || !is_null($request->head_rejected_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                }
                            } elseif ($prev->role_key === 'manager') {
                                if (is_null($request->manager_approved_at) || !is_null($request->manager_rejected_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                }
                            }
                        }
                        $shouldAppearInSpvPending = $allPreviousApproved
                            && is_null($request->supervisor_approved_at)
                            && is_null($request->supervisor_rejected_at);
                    }
                }
            }
        }

        return response()->json([
            'request' => [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'request_type' => $request->request_type,
                'status' => $request->status,
                'current_approval_order' => $request->current_approval_order,
                'employee_id' => $request->employee_id,
                'employee_name' => $request->employee->name ?? 'N/A',
                'employee_divisi' => $request->employee->divisi ?? 'N/A',
                'supervisor_approved_at' => $request->supervisor_approved_at,
                'supervisor_rejected_at' => $request->supervisor_rejected_at,
                'head_approved_at' => $request->head_approved_at,
                'head_rejected_at' => $request->head_rejected_at,
                'manager_approved_at' => $request->manager_approved_at,
                'manager_rejected_at' => $request->manager_rejected_at,
                'hr_approved_at' => $request->hr_approved_at,
                'hr_rejected_at' => $request->hr_rejected_at,
            ],
            'approval_flow_global' => $approvalFlowGlobal ? $approvalFlowGlobal->map(function ($setting) {
                return [
                    'approval_order' => $setting->approval_order,
                    'role_key' => $setting->role_key,
                    'description' => $setting->description,
                    'is_active' => $setting->is_active,
                ];
            }) : null,
            'approval_flow_filtered' => $approvalFlow->map(function ($setting) {
                return [
                    'approval_order' => $setting->approval_order,
                    'role_key' => $setting->role_key,
                    'description' => $setting->description,
                    'is_active' => $setting->is_active,
                ];
            }),
            'divisi_approval_setting' => $divisiSetting ? [
                'divisi_id' => $divisiSetting->divisi_id,
                'spv_enabled' => $divisiSetting->spv_enabled,
                'head_enabled' => $divisiSetting->head_enabled,
                'manager_enabled' => $divisiSetting->manager_enabled,
                'is_active' => $divisiSetting->is_active,
            ] : null,
            'approval_chain' => array_map(function ($chainData) {
                return [
                    'approval_order' => $chainData['approval_order'] ?? null,
                    'role_key' => $chainData['role_key'] ?? null,
                    'level_name' => $chainData['level_name'] ?? null,
                    'user_count' => $chainData['user_count'] ?? 0,
                    'user_id' => $chainData['user_id'] ?? null,
                ];
            }, $approvalChain),
            'next_approver_from_flow' => $nextApprover ? [
                'approval_order' => $nextApprover->approval_order,
                'role_key' => $nextApprover->role_key,
                'description' => $nextApprover->description,
            ] : null,
            'next_approver_from_chain' => $nextApproverFromChain ? [
                'approval_order' => $nextApproverFromChain['approval_order'] ?? null,
                'role_key' => $nextApproverFromChain['role_key'] ?? null,
                'level_name' => $nextApproverFromChain['level_name'] ?? null,
            ] : null,
            'should_appear_in_spv_pending' => $shouldAppearInSpvPending,
            'debug_info' => [
                'current_order' => $currentOrder,
                'request_type' => $request->request_type,
                'employee_divisi' => $divisi,
                'spv_exists_in_global_flow' => $approvalFlowGlobal ? ($approvalFlowGlobal->firstWhere('role_key', 'spv_division') !== null) : false,
                'spv_order_in_global' => $approvalFlowGlobal ? (($spvSetting = $approvalFlowGlobal->firstWhere('role_key', 'spv_division')) ? $spvSetting->approval_order : null) : null,
                'spv_enabled_in_divisi' => $divisiSetting ? ($divisiSetting->spv_enabled ?? false) : false,
                'spv_in_chain' => isset($approvalChain['order_' . ($approvalFlowGlobal ? (($spvSetting = $approvalFlowGlobal->firstWhere('role_key', 'spv_division')) ? $spvSetting->approval_order : null) : null)]),
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Display pending approvals for supervisor
     * Shows Form Karyawan, Vehicle, Asset (Overtime skip untuk sekarang)
     * Hanya bisa diakses oleh SPV (jabatan = 5)
     */
    public function supervisorPending()
    {
        $user = Auth::user();

        // Authorization: Hanya SPV yang bisa akses
        if ($user->jabatan != 5) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya SPV yang dapat mengakses.');
        }

        // Get all pending requests for SPV level
        // ApprovalService akan otomatis cek ApprovalSetting dan DivisiApprovalSetting (untuk absence)
        $allPendingRequests = ApprovalService::getPendingRequestsForUser($user, null, null);

        // dd($allPendingRequests);

        // Separate by request type
        // SPV hanya melihat request dari divisi yang SAMA
        // Load employee dengan divisiUser untuk mendapatkan nama divisi
        $formRequests = $allPendingRequests->filter(function ($request) use ($user) {
            // Get employee's divisi untuk validasi
            $employeeDivisi = null;
            if ($request->employee_id) {
                // Load employee dengan divisiUser jika belum di-load
                if (!$request->relationLoaded('employee')) {
                    $request->load('employee.divisiUser');
                }
                $employeeDivisi = $request->employee ? $request->employee->divisi : null;
            }

            return $request instanceof EmployeeRequest
                && in_array($request->request_type, ['shift_change', 'absence'])
                && $request->status === EmployeeRequest::STATUS_PENDING
                && $employeeDivisi == $user->divisi; // Hanya divisi yang SAMA
        });

        // Overtime: skip untuk sekarang, sistemnya nanti sendiri
        $overtimeRequests = collect();

        // Vehicle & Asset: SPV hanya melihat request dari divisi yang SAMA
        // PENTING: Mengikuti approval flow dari ApprovalSetting untuk 'vehicle_asset'
        $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
        $spvInVehicleFlow = false;
        $spvApprovalOrder = null;

        foreach ($vehicleAssetFlow as $setting) {
            if (
                $setting->role_key === 'spv_division' ||
                ($setting->role_key === 'head_division' && (int) $user->jabatan === 5 && $setting->isUserAllowedToApprove($user))
            ) {
                $spvInVehicleFlow = true;
                $spvApprovalOrder = $setting->approval_order;
                break;
            }
        }

        $vehicleRequests = collect();
        $assetRequests = collect();

        if ($spvInVehicleFlow) {
            // Jika SPV ada di flow, cek apakah approval order sudah sampai ke SPV
            if ($spvApprovalOrder == 1) {
                // SPV adalah approver pertama, query yang belum di-approve manager
                $vehicleRequests = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->get();

                $assetRequests = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->get();
            }
            // Jika SPV bukan order 1, tidak ada VehicleAssetRequest untuk SPV (karena approval sebelumnya belum selesai)
        }

        // Check which requests can be approved by current user
        // Load employee.divisiUser for all requests in collection
        $formRequests->each(function ($request) {
            if ($request->relationLoaded('employee') && $request->employee) {
                $request->employee->loadMissing('divisiUser');
            } elseif ($request->employee_id) {
                $request->loadMissing('employee.divisiUser');
            }
        });

        $approvalService = new ApprovalService();
        $formRequestsWithAccess = $formRequests->map(function ($request) use ($user, $approvalService) {
            $canApprove = $this->canApproveRequest($user, $request);
            $request->can_approve = $canApprove;

            // Get reason why cannot approve if false
            if (!$canApprove) {
                $request->cannot_approve_reason = $this->getCannotApproveReason($user, $request);
            } else {
                $request->cannot_approve_reason = null;
            }

            return $request;
        });

        // Get approval info for card
        $approvalInfo = [
            'can_approve_absence' => false,
            'can_approve_shift_change' => false,
            'divisi' => $user->divisi,
            'jabatan' => $user->jabatan,
            'absence_approval_order' => [], // Urutan approval untuk absence
        ];

        // Check if SPV can approve absence requests
        $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $user->divisi)
            ->where('is_active', true)
            ->first();

        if ($divisiSetting && ($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
            $approvalInfo['can_approve_absence'] = true;

            // Get approval flow untuk absence di divisi ini
            $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence', $user->divisi);

            // Build urutan approval dengan status enabled/disabled
            $orderList = [];
            foreach ($absenceFlow as $setting) {
                $roleKey = $setting->role_key;
                $order = $setting->approval_order;
                $isEnabled = false;
                $roleLabel = '';

                if ($roleKey === 'spv_division') {
                    $isEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                    $roleLabel = 'SPV';
                } elseif ($roleKey === 'head_division') {
                    $isEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                    $roleLabel = 'HEAD';
                } elseif ($roleKey === 'manager') {
                    $isEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                    $roleLabel = 'MANAGER';
                } elseif ($roleKey === 'hr') {
                    $isEnabled = true; // HR selalu enabled
                    $roleLabel = 'HR';
                }

                $orderList[] = [
                    'order' => $order,
                    'role' => $roleLabel,
                    'enabled' => $isEnabled,
                    'is_current_user' => ($roleKey === 'spv_division' && (int) $user->jabatan === 5),
                ];
            }

            // Sort by order
            usort($orderList, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });

            $approvalInfo['absence_approval_order'] = $orderList;
        }

        // Check if SPV can approve shift_change (always enabled if SPV in flow)
        $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');
        $spvInShiftFlow = $shiftChangeFlow->contains(function ($setting) {
            return $setting->role_key === 'spv_division';
        });
        $approvalInfo['can_approve_shift_change'] = $spvInShiftFlow;

        return view('hr.approval.supervisor-pending', compact('formRequestsWithAccess', 'overtimeRequests', 'vehicleRequests', 'assetRequests', 'approvalInfo'));
    }

    /**
     * Get reason why user cannot approve a request
     */
    private function getCannotApproveReason($user, EmployeeRequest $request)
    {
        // Get requester divisi
        $requesterDivisi = null;
        if ($request->employee_id) {
            $employee = User::find($request->employee_id);
            $requesterDivisi = $employee ? $employee->divisi : null;
        }

        // 1. Check request status first
        $approvedStatuses = [
            EmployeeRequest::STATUS_PENDING,
            EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
            EmployeeRequest::STATUS_HEAD_APPROVED,
            EmployeeRequest::STATUS_MANAGER_APPROVED,
        ];
        if (!in_array($request->status, $approvedStatuses)) {
            return "Status permohonan tidak dalam proses approval ({$request->status})";
        }

        // 2. Check divisi match (for SPV/HEAD/MANAGER, not HR)
        // PENTING: HR TIDAK MEMANDANG DIVISI - HR bisa approve dari semua divisi
        // JANGAN cek divisi untuk HR - HR bisa approve dari semua divisi
        if (!$user->isHR() && in_array((int) $user->jabatan, [3, 4, 5]) && (int) $requesterDivisi !== (int) $user->divisi) {
            return "Divisi tidak match (Pemohon: Divisi " . ($requesterDivisi ?? 'N/A') . ", Anda: Divisi {$user->divisi})";
        }

        // 2a. KHUSUS UNTUK REQUEST DARI MANAGER: Cek apakah General Manager sudah approve sebelum HR bisa approve
        if ($user->isHR() && $request->employee && (int) $request->employee->jabatan === 3) {
            if (is_null($request->general_approved_at)) {
                return "General Manager belum approve permohonan ini";
            }
            if (!is_null($request->general_rejected_at)) {
                return "General Manager sudah menolak permohonan ini";
            }
        }

        // 2b. KHUSUS UNTUK REQUEST DARI HEAD PRODUKSI (jabatan 4, divisi 4): Cek apakah General Manager sudah approve sebelum HR bisa approve
        if ($user->isHR() && $request->employee && (int) $request->employee->jabatan === 4 && (int) $request->employee->divisi === 4) {
            if (is_null($request->general_approved_at)) {
                return "General Manager belum approve permohonan ini";
            }
            if (!is_null($request->general_rejected_at)) {
                return "General Manager sudah menolak permohonan ini";
            }
        }

        // 3. Get approval flow
        // Untuk HEAD PRODUKSI, gunakan approval flow khusus (General Manager → HRD)
        $isHeadProduksi = $request->employee && (int) $request->employee->jabatan === 4 && (int) $request->employee->divisi === 4;
        if ($isHeadProduksi) {
            // Untuk HEAD PRODUKSI, approval flow hanya: General Manager (order 1) → HRD (order 2)
            $approvalFlow = collect([
                (object) [
                    'role_key' => 'general_manager',
                    'approval_order' => 1,
                    'description' => 'General Manager',
                    'is_active' => true,
                    'allowed_jabatan' => [],
                ],
                (object) [
                    'role_key' => 'hr',
                    'approval_order' => 2,
                    'description' => 'HRD',
                    'is_active' => true,
                    'allowed_jabatan' => [],
                ],
            ]);
        } else {
            $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        }

        // 4. Check DivisiApprovalSetting for absence
        $divisiSetting = null;
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $requesterDivisi) {
            $divisiSetting = DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();

            if (!$divisiSetting) {
                return "Setting approval untuk divisi pemohon belum dikonfigurasi";
            }
        }

        // 5. Determine user role and find corresponding setting
        $userRoleKey = null;
        $userSetting = null;

        if ($user->isHR()) {
            $userRoleKey = 'hr';
            $userSetting = $approvalFlow->firstWhere('role_key', 'hr');
        } elseif ((int) $user->jabatan === 5) {
            $userRoleKey = 'spv_division';
            $userSetting = $approvalFlow->firstWhere('role_key', 'spv_division');

            // Check if SPV is enabled for absence
            if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $divisiSetting) {
                if (!($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
                    return "SPV disabled untuk divisi ini di setting approval";
                }
            }
        } elseif ((int) $user->jabatan === 4) {
            $userRoleKey = 'head_division';
            $userSetting = $approvalFlow->firstWhere('role_key', 'head_division');

            // Check if HEAD is enabled for absence
            if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $divisiSetting) {
                if (!($divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true)) {
                    return "HEAD disabled untuk divisi ini di setting approval";
                }
            }
        } elseif ((int) $user->jabatan === 3) {
            $userRoleKey = 'manager';
            $userSetting = $approvalFlow->firstWhere('role_key', 'manager');

            // Check if MANAGER is enabled for absence
            if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $divisiSetting) {
                if (!($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                    return "Manager disabled untuk divisi ini di setting approval";
                }
            }
        }

        if (!$userSetting) {
            $roleLabel = $userRoleKey === 'hr' ? 'HR' : ($userRoleKey === 'spv_division' ? 'SPV' : ($userRoleKey === 'head_division' ? 'HEAD' : ($userRoleKey === 'manager' ? 'Manager' : 'Approver')));
            return "{$roleLabel} tidak ada di approval flow untuk jenis permohonan ini";
        }

        // 6. Check if user is allowed to approve
        // Skip pengecekan untuk approval flow khusus HEAD PRODUKSI (karena menggunakan stdClass, bukan ApprovalSetting)
        if (!$isHeadProduksi) {
            $context = ['requester_divisi' => $requesterDivisi];
            if (!$userSetting->isUserAllowedToApprove($user, $context)) {
                return "Anda bukan approver yang ditentukan untuk permohonan ini";
            }
        }

        // 7. Check current approval order
        $currentOrder = $request->current_approval_order ?? 0;
        $userOrder = $userSetting->approval_order;

        // Debug: Log detail untuk troubleshooting
        Log::debug('getCannotApproveReason - User Check', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'request_type' => $request->request_type,
            'user_role' => $userRoleKey,
            'current_order' => $currentOrder,
            'user_order' => $userOrder,
            'supervisor_approved_at' => $request->supervisor_approved_at,
            'supervisor_rejected_at' => $request->supervisor_rejected_at,
            'head_approved_at' => $request->head_approved_at,
            'manager_approved_at' => $request->manager_approved_at,
            'hr_approved_at' => $request->hr_approved_at,
            'status' => $request->status,
        ]);

        if ($currentOrder >= $userOrder) {
            return "Permohonan sudah melewati tahap approval Anda (Current: {$currentOrder}, Order Anda: {$userOrder})";
        }

        // 8. Check if previous approvers have approved (only if SPV is not first approver)
        if ($userOrder > 1) {
            for ($i = 1; $i < $userOrder; $i++) {
                $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                if ($prevSetting) {
                    // For ABSENCE, check if previous level is enabled
                    if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $divisiSetting) {
                        $prevRoleKey = $prevSetting->role_key;
                        $isPrevLevelEnabled = true;

                        if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                        } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                            $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                        } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                            $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                        }

                        if (!$isPrevLevelEnabled) {
                            continue; // Skip disabled levels
                        }
                    }

                    // Check if previous level has been approved
                    // PENTING: Untuk approval_order yang memiliki multiple allowed_jabatan (seperti ["3","4"]),
                    // kita harus cek apakah salah satu dari role yang diizinkan sudah approve
                    $prevRoleKey = $prevSetting->role_key;
                    $prevAllowedJabatan = $prevSetting->allowed_jabatan ?? [];

                    $prevApproved = false;

                    // Jika allowed_jabatan ada dan berisi multiple jabatan, cek semua yang diizinkan
                    if (!empty($prevAllowedJabatan) && is_array($prevAllowedJabatan) && count($prevAllowedJabatan) > 1) {
                        // Multiple allowed_jabatan: cek apakah salah satu sudah approve
                        foreach ($prevAllowedJabatan as $jabatan) {
                            if ((int) $jabatan === 3 && !is_null($request->manager_approved_at)) {
                                $prevApproved = true;
                                break;
                            } elseif ((int) $jabatan === 4 && !is_null($request->head_approved_at)) {
                                $prevApproved = true;
                                break;
                            } elseif ((int) $jabatan === 5 && !is_null($request->supervisor_approved_at)) {
                                $prevApproved = true;
                                break;
                            }
                        }
                    } else {
                        // Single role_key: gunakan logika lama
                        if ($prevRoleKey === 'spv_division') {
                            $prevApproved = !is_null($request->supervisor_approved_at);
                        } elseif ($prevRoleKey === 'head_division') {
                            $prevApproved = !is_null($request->head_approved_at);
                        } elseif ($prevRoleKey === 'manager') {
                            $prevApproved = !is_null($request->manager_approved_at);
                        } elseif ($prevRoleKey === 'general_manager') {
                            // Untuk General Manager, cek general_approved_at
                            $prevApproved = !is_null($request->general_approved_at);
                        } elseif ($prevRoleKey === 'hr') {
                            $prevApproved = !is_null($request->hr_approved_at);
                        }
                    }

                    if (!$prevApproved) {
                        $roleLabel = $prevRoleKey === 'spv_division' ? 'SPV' : ($prevRoleKey === 'head_division' ? 'HEAD' : ($prevRoleKey === 'manager' ? 'Manager' : ($prevRoleKey === 'general_manager' ? 'General Manager' : ($prevRoleKey === 'hr' ? 'HR' : 'Approver'))));

                        // Jika multiple allowed_jabatan, tampilkan semua role yang diizinkan
                        if (!empty($prevAllowedJabatan) && is_array($prevAllowedJabatan) && count($prevAllowedJabatan) > 1) {
                            $allowedRoles = [];
                            foreach ($prevAllowedJabatan as $jabatan) {
                                if ((int) $jabatan === 3) $allowedRoles[] = 'Manager';
                                elseif ((int) $jabatan === 4) $allowedRoles[] = 'HEAD';
                                elseif ((int) $jabatan === 5) $allowedRoles[] = 'SPV';
                            }
                            $roleLabel = implode(' atau ', $allowedRoles);
                        }

                        return "Level sebelumnya ({$roleLabel}) belum approve";
                    }
                }
            }
        }

        // If all checks pass but canApproveRequest still returns false, log for debugging
        Log::warning('getCannotApproveReason: All checks passed but request cannot be approved', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'request_type' => $request->request_type,
            'user_id' => $user->id,
            'user_jabatan' => $user->jabatan,
            'user_divisi' => $user->divisi,
            'requester_divisi' => $requesterDivisi,
            'current_order' => $currentOrder,
            'user_order' => $userOrder,
            'status' => $request->status,
            'supervisor_approved_at' => $request->supervisor_approved_at,
            'supervisor_rejected_at' => $request->supervisor_rejected_at,
            'head_approved_at' => $request->head_approved_at,
            'manager_approved_at' => $request->manager_approved_at,
            'hr_approved_at' => $request->hr_approved_at,
        ]);

        // Cek apakah ada masalah dengan current_approval_order yang tidak sesuai
        // Jika currentOrder adalah 0 tapi ada approval sebelumnya, kemungkinan ada masalah
        if ($currentOrder == 0 && ($request->supervisor_approved_at || $request->head_approved_at || $request->manager_approved_at)) {
            return "Data approval tidak konsisten (current_order: 0 tapi ada approval sebelumnya) - silakan hubungi admin";
        }

        return "Tidak dapat menentukan alasan (semua kondisi terpenuhi - cek log untuk detail)";
    }

    /**
     * Display pending approvals for HEAD DIVISI
     * Mengikuti ApprovalSetting: hanya tampil jika HEAD DIVISI ada di approval flow
     * Hanya bisa diakses oleh HEAD DIVISI (user dengan canApprove() = true)
     */
    public function headPending()
    {
        $user = Auth::user();

        // Authorization: Hanya HEAD DIVISI yang bisa akses
        if (!method_exists($user, 'canApprove') || !$user->canApprove()) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya HEAD DIVISI yang dapat mengakses.');
        }

        // Menggunakan query langsung seperti di managerPending()
        // HEAD DIVISI hanya melihat request dari divisi yang SAMA
        $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

        // EXCEPTION: Head Divisi Produksi (4) juga melihat request dari Prepress (3)
        if ($user->divisi == 4) {
            $prepressEmployeeIds = \App\Models\User::where('divisi', 3)->pluck('id');
            $employeeIds = $employeeIds->merge($prepressEmployeeIds);
        }

        $formRequests = collect();

        foreach (['shift_change', 'absence'] as $requestType) {
            // Untuk absence, gunakan divisi head untuk mendapatkan approval flow yang sesuai
            $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
            $flow = \App\Models\ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

            // Get divisi approval setting untuk absence
            $divisiSetting = null;
            if ($requestType === 'absence') {
                $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $user->divisi)
                    ->where('is_active', true)
                    ->first();
            }

            // Pastikan HEAD memang ada di flow
            $headSetting = null;
            foreach ($flow as $setting) {
                if ($setting->role_key === 'head_division') {
                    $headSetting = $setting;
                    break;
                }
            }
            if (!$headSetting) {
                continue; // HEAD tidak ada di flow untuk request type ini, skip
            }

            $order = $headSetting->approval_order;
            $query = \App\Models\EmployeeRequest::whereIn('employee_id', $employeeIds)
                ->where('request_type', $requestType)
                ->whereNull('head_approved_at')
                ->whereNull('head_rejected_at')
                // Exclude request yang sudah di-approve/reject oleh HRD atau sudah selesai
                ->whereNull('hr_approved_at')
                ->whereNull('hr_rejected_at')
                ->whereNotIn('status', [
                    \App\Models\EmployeeRequest::STATUS_HR_APPROVED,
                    \App\Models\EmployeeRequest::STATUS_HR_REJECTED,
                    \App\Models\EmployeeRequest::STATUS_CANCELLED
                ]);

            if ($order == 1) {
                // Head adalah approver pertama, belum ada approval sebelumnya
                $query->whereNull('supervisor_approved_at')
                    ->whereNull('supervisor_rejected_at');
            } else {
                // Semua approver sebelum HEAD harus sudah approved
                // PENTING: Hanya cek manager jika manager ada di flow SEBELUM HEAD DAN enabled (untuk absence)
                $query->where(function ($q) use ($flow, $order, $requestType, $divisiSetting) {
                    for ($i = 1; $i < $order; $i++) {
                        $prev = $flow->firstWhere('approval_order', $i);
                        if (!$prev) {
                            continue;
                        }

                        if ($prev->role_key === 'spv_division') {
                            $q->whereNotNull('supervisor_approved_at')
                                ->whereNull('supervisor_rejected_at');
                        } elseif ($prev->role_key === 'manager') {
                            // Cek apakah manager enabled (untuk absence) atau ada di flow (untuk shift_change)
                            $managerEnabled = true;
                            if ($requestType === 'absence' && $divisiSetting) {
                                $managerEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                            }

                            // Hanya cek manager_approved_at jika manager enabled/ada di flow
                            if ($managerEnabled) {
                                // Manager enabled: pastikan manager sudah approve
                                $q->whereNotNull('manager_approved_at')
                                    ->whereNull('manager_rejected_at');
                            } else {
                                // Manager tidak enabled: pastikan manager_approved_at masih NULL
                                // (karena manager tidak akan approve request yang tidak ada di chain-nya)
                                $q->whereNull('manager_approved_at')
                                    ->whereNull('manager_rejected_at');
                            }
                        }
                    }
                });
            }

            $formRequests = $formRequests->merge($query->get());
        }

        // Overtime: skip untuk sekarang, sistemnya nanti sendiri
        $overtimeRequests = collect();

        // SPL (Surat Perintah Lembur): ambil SPL pending untuk HEAD
        $splRequests = collect();
        try {
            $splApprovalService = new \App\Services\SplApprovalService();
            // getPendingRequestsForUser sudah melakukan eager loading di dalamnya
            $splRequests = $splApprovalService::getPendingRequestsForUser($user);
        } catch (\Exception $e) {
            \Log::error('Error getting SPL pending for HEAD: ' . $e->getMessage());
        }

        // Vehicle/Asset: HEAD DIVISI biasanya tidak handle vehicle/asset
        // PENTING: Untuk HEAD PRODUKSI (jabatan 4, divisi 4), filter out request dari HEAD PRODUKSI sendiri
        // Karena request dari HEAD PRODUKSI langsung ke General Manager, bukan ke HEAD biasa
        $vehicleQuery = VehicleAssetRequest::where('request_type', 'vehicle')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);

        $assetQuery = VehicleAssetRequest::where('request_type', 'asset')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);

        // Filter out request dari HEAD PRODUKSI jika user adalah HEAD PRODUKSI
        if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
            $headProduksiIds = \App\Models\User::where('jabatan', 4)->where('divisi', 4)->pluck('id');
            $vehicleQuery->whereNotIn('employee_id', $headProduksiIds);
            $assetQuery->whereNotIn('employee_id', $headProduksiIds);
        }

        $vehicleRequests = $vehicleQuery->get();
        $assetRequests = $assetQuery->get();

        return view('hr.approval.head-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests', 'splRequests'));
    }

    // public function headPending()
    // {
    //     $user = Auth::user();

    //     // Authorization: Hanya HEAD DIVISI yang bisa akses
    //     if (!method_exists($user, 'canApprove') || !$user->canApprove()) {
    //         abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya HEAD DIVISI yang dapat mengakses.');
    //     }

    //     // Menggunakan query langsung seperti di managerPending()
    //     // HEAD DIVISI hanya melihat request dari divisi yang SAMA
    //     $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

    //     // EXCEPTION: Head Divisi Produksi (4) juga melihat request dari Prepress (3)
    //     if ($user->divisi == 4) {
    //         $prepressEmployeeIds = \App\Models\User::where('divisi', 3)->pluck('id');
    //         $employeeIds = $employeeIds->merge($prepressEmployeeIds);
    //     }

    //     $formRequests = collect();

    //     foreach (['shift_change', 'absence'] as $requestType) {
    //         // Untuk absence, gunakan divisi head untuk mendapatkan approval flow yang sesuai
    //         $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
    //         $flow = \App\Models\ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

    //         // Get divisi approval setting untuk absence
    //         $divisiSetting = null;
    //         if ($requestType === 'absence') {
    //             $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $user->divisi)
    //                 ->where('is_active', true)
    //                 ->first();
    //         }

    //         // Pastikan HEAD memang ada di flow
    //         $headSetting = null;
    //         foreach ($flow as $setting) {
    //             if ($setting->role_key === 'head_division') {
    //                 $headSetting = $setting;
    //                 break;
    //             }
    //         }
    //         if (!$headSetting) {
    //             continue; // HEAD tidak ada di flow untuk request type ini, skip
    //         }

    //         $order = $headSetting->approval_order;
    //         $query = \App\Models\EmployeeRequest::whereIn('employee_id', $employeeIds)
    //             ->where('request_type', $requestType)
    //             ->whereNull('head_approved_at')
    //             ->whereNull('head_rejected_at')
    //             // Exclude request yang sudah di-approve/reject oleh HRD atau sudah selesai
    //             ->whereNull('hr_approved_at')
    //             ->whereNull('hr_rejected_at')
    //             ->whereNotIn('status', [
    //                 \App\Models\EmployeeRequest::STATUS_HR_APPROVED,
    //                 \App\Models\EmployeeRequest::STATUS_HR_REJECTED,
    //                 \App\Models\EmployeeRequest::STATUS_CANCELLED
    //             ]);

    //         if ($order == 1) {
    //             // Head adalah approver pertama, belum ada approval sebelumnya
    //             $query->whereNull('supervisor_approved_at')
    //                 ->whereNull('supervisor_rejected_at');
    //         } else {
    //             // Semua approver sebelum HEAD harus sudah approved
    //             // PENTING: Hanya cek manager jika manager ada di flow SEBELUM HEAD DAN enabled (untuk absence)
    //             $query->where(function ($q) use ($flow, $order, $requestType, $divisiSetting) {
    //                 for ($i = 1; $i < $order; $i++) {
    //                     $prev = $flow->firstWhere('approval_order', $i);
    //                     if (!$prev) {
    //                         continue;
    //                     }

    //                     if ($prev->role_key === 'spv_division') {
    //                         $q->whereNotNull('supervisor_approved_at')
    //                             ->whereNull('supervisor_rejected_at');
    //                     } elseif ($prev->role_key === 'manager') {
    //                         // Cek apakah manager enabled (untuk absence) atau ada di flow (untuk shift_change)
    //                         $managerEnabled = true;
    //                         if ($requestType === 'absence' && $divisiSetting) {
    //                             $managerEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
    //                         }

    //                         // Hanya cek manager_approved_at jika manager enabled/ada di flow
    //                         if ($managerEnabled) {
    //                             // Manager enabled: pastikan manager sudah approve
    //                             $q->whereNotNull('manager_approved_at')
    //                                 ->whereNull('manager_rejected_at');
    //                         } else {
    //                             // Manager tidak enabled: pastikan manager_approved_at masih NULL
    //                             // (karena manager tidak akan approve request yang tidak ada di chain-nya)
    //                             $q->whereNull('manager_approved_at')
    //                                 ->whereNull('manager_rejected_at');
    //                         }
    //                     }
    //                 }
    //             });
    //         }

    //         $formRequests = $formRequests->merge($query->get());
    //     }

    //     // Overtime: skip untuk sekarang, sistemnya nanti sendiri
    //     $overtimeRequests = collect();

    //     // Vehicle/Asset: HEAD DIVISI biasanya tidak handle vehicle/asset
    //     $vehicleRequests = VehicleAssetRequest::where('request_type', 'vehicle')
    //         ->where('divisi_id', $user->divisi)
    //         ->whereNull('manager_at')
    //         ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER)
    //         ->get();

    //     $assetRequests = VehicleAssetRequest::where('request_type', 'asset')
    //         ->where('divisi_id', $user->divisi)
    //         ->whereNull('manager_at')
    //         ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER)
    //         ->get();

    //     return view('hr.approval.head-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests'));
    // }

    // public function headPending()
    // {
    //     $user = Auth::user();

    //     // Authorization: Hanya HEAD DIVISI yang bisa akses
    //     if (!method_exists($user, 'canApprove') || !$user->canApprove()) {
    //         abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya HEAD DIVISI yang dapat mengakses.');
    //     }

    //     // Menggunakan query langsung seperti di managerPending()
    //     // HEAD DIVISI hanya melihat request dari divisi yang SAMA
    //     $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

    //     $formRequests = collect();

    //     foreach (['shift_change', 'absence'] as $requestType) {
    //         // Untuk absence, gunakan divisi head untuk mendapatkan approval flow yang sesuai
    //         $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
    //         $flow = \App\Models\ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

    //         // Pastikan HEAD memang ada di flow
    //         $headSetting = null;
    //         foreach ($flow as $setting) {
    //             if ($setting->role_key === 'head_division') {
    //                 $headSetting = $setting;
    //                 break;
    //             }
    //         }
    //         if (!$headSetting) {
    //             continue; // HEAD tidak ada di flow untuk request type ini, skip
    //         }

    //         $order = $headSetting->approval_order;
    //         $query = \App\Models\EmployeeRequest::whereIn('employee_id', $employeeIds)
    //             ->where('request_type', $requestType)
    //             ->whereNull('head_approved_at')
    //             ->whereNull('head_rejected_at');

    //         if ($order == 1) {
    //             // Head adalah approver pertama, belum ada approval sebelumnya
    //             $query->whereNull('supervisor_approved_at')
    //                   ->whereNull('supervisor_rejected_at');
    //         } else {
    //             // Semua approver sebelum HEAD harus sudah approved
    //             $query->where(function($q) use ($flow, $order) {
    //                 for ($i = 1; $i < $order; $i++) {
    //                     $prev = $flow->firstWhere('approval_order', $i);
    //                     if (!$prev) { continue; }
    //                     if ($prev->role_key === 'spv_division') {
    //                         $q->whereNotNull('supervisor_approved_at')
    //                           ->whereNull('supervisor_rejected_at');
    //                     } elseif ($prev->role_key === 'manager') {
    //                         $q->whereNotNull('manager_approved_at')
    //                           ->whereNull('manager_rejected_at');
    //                     }
    //                 }
    //             });
    //         }

    //         $formRequests = $formRequests->merge($query->get());
    //     }

    //     // Overtime: skip untuk sekarang, sistemnya nanti sendiri
    //     $overtimeRequests = collect();

    //     // Vehicle/Asset: HEAD DIVISI biasanya tidak handle vehicle/asset
    //     $vehicleRequests = collect();
    //     $assetRequests = collect();

    //     return view('hr.approval.head-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests'));
    // }

    /**
     * Display pending approvals for HR
     * Mengikuti ApprovalSetting: hanya tampil jika HRD ada di approval flow
     * Hanya bisa diakses oleh HRD
     */
    public function hrPending()
    {
        $user = Auth::user();

        // Authorization: Hanya HRD yang bisa akses
        if (!method_exists($user, 'isHR') || !$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya HRD yang dapat mengakses.');
        }

        // Get all pending requests for HR level using ApprovalService
        // Jangan filter role_key, biarkan ApprovalService cek berdasarkan allowed_jabatan
        $allPendingRequests = ApprovalService::getPendingRequestsForUser($user, null, null);


        // Separate by request type
        // Filter: permohonan yang bisa di-approve oleh HR berdasarkan approval flow
        $formRequests = $allPendingRequests->filter(function ($request) {
            return $request instanceof EmployeeRequest
                && in_array($request->request_type, ['shift_change', 'absence'])
                && $request->canBeApprovedByHR();
        });

        // Load employee.divisiUser for all requests
        $formRequests->each(function ($request) {
            if ($request->relationLoaded('employee') && $request->employee) {
                $request->employee->loadMissing('divisiUser');
            } elseif ($request->employee_id) {
                $request->loadMissing('employee.divisiUser');
            }
        });

        // Check which requests can be approved by current user
        $approvalService = new ApprovalService();
        $formRequestsWithAccess = $formRequests->map(function ($request) use ($user, $approvalService) {
            $canApprove = $this->canApproveRequest($user, $request);
            // dd($canApprove);
            $request->can_approve = $canApprove;

            // Get reason why cannot approve if false
            if (!$canApprove) {
                $request->cannot_approve_reason = $this->getCannotApproveReason($user, $request);
            } else {
                $request->cannot_approve_reason = null;
            }

            return $request;
        });

        // Get approval info for card
        $approvalInfo = [
            'can_approve_absence' => false,
            'can_approve_shift_change' => false,
            'absence_approval_order' => [],
            'shift_change_approval_order' => [],
        ];

        // Check if HR can approve absence requests
        $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence');
        $hrInAbsenceFlow = $absenceFlow->contains(function ($setting) use ($user) {
            return $setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user);
        });
        $approvalInfo['can_approve_absence'] = $hrInAbsenceFlow;

        if ($hrInAbsenceFlow) {
            // Build urutan approval untuk absence
            $orderList = [];
            foreach ($absenceFlow as $setting) {
                $roleKey = $setting->role_key;
                $order = $setting->approval_order;
                $roleLabel = '';

                if ($roleKey === 'spv_division') {
                    $roleLabel = 'SPV';
                } elseif ($roleKey === 'head_division') {
                    $roleLabel = 'HEAD';
                } elseif ($roleKey === 'manager') {
                    $roleLabel = 'MANAGER';
                } elseif ($roleKey === 'hr') {
                    $roleLabel = 'HR';
                }

                if ($roleLabel) {
                    $orderList[] = [
                        'order' => $order,
                        'role' => $roleLabel,
                        'enabled' => true,
                        'is_current_user' => ($roleKey === 'hr'),
                    ];
                }
            }

            usort($orderList, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });

            $approvalInfo['absence_approval_order'] = $orderList;
        }

        // Check if HR can approve shift_change
        $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');
        $hrInShiftFlow = $shiftChangeFlow->contains(function ($setting) use ($user) {
            return $setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user);
        });
        $approvalInfo['can_approve_shift_change'] = $hrInShiftFlow;

        if ($hrInShiftFlow) {
            // Build urutan approval untuk shift_change
            $orderList = [];
            foreach ($shiftChangeFlow as $setting) {
                $roleKey = $setting->role_key;
                $order = $setting->approval_order;
                $roleLabel = '';

                if ($roleKey === 'spv_division') {
                    $roleLabel = 'SPV';
                } elseif ($roleKey === 'head_division') {
                    $roleLabel = 'HEAD';
                } elseif ($roleKey === 'manager') {
                    $roleLabel = 'MANAGER';
                } elseif ($roleKey === 'hr') {
                    $roleLabel = 'HR';
                }

                if ($roleLabel) {
                    $orderList[] = [
                        'order' => $order,
                        'role' => $roleLabel,
                        'enabled' => true,
                        'is_current_user' => ($roleKey === 'hr'),
                    ];
                }
            }

            usort($orderList, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });

            $approvalInfo['shift_change_approval_order'] = $orderList;
        }

        // Overtime: skip untuk sekarang, sistemnya nanti sendiri
        $overtimeRequests = collect();

        // SPL (Surat Perintah Lembur): ambil SPL pending untuk HR
        // HR mengambil SPL yang sudah head_approved atau manager_approved dan belum di-approve HR
        $splRequests = collect();
        try {
            // Cek apakah HR ada di approval flow untuk SPL
            $splFlow = \App\Models\ApprovalSetting::getApprovalFlow('spl');
            $hrInSplFlow = $splFlow->contains(function ($setting) use ($user) {
                return $setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user);
            });

            if ($hrInSplFlow) {
                // Ambil SPL yang sudah head_approved atau manager_approved dan belum di-approve HR
                $splQuery = \App\Models\SplRequest::with([
                    'supervisor',
                    'supervisor.divisiUser',
                    'divisi',
                    'employees',
                    'head',
                    'manager',
                    'hrd'
                ])->whereIn('status', [
                    \App\Models\SplRequest::STATUS_HEAD_APPROVED,
                    \App\Models\SplRequest::STATUS_MANAGER_APPROVED,
                ])
                    ->whereNull('hrd_approved_at')
                    ->whereNull('hrd_rejected_at')
                    ->where('status', '!=', \App\Models\SplRequest::STATUS_REJECTED)
                    ->orderBy('created_at', 'desc');

                $allSplRequests = $splQuery->get();

                // Filter: hanya SPL yang approval chain-nya sudah sampai ke HR dan level sebelumnya sudah di-approve
                $splApprovalService = new \App\Services\SplApprovalService();
                $splRequests = $allSplRequests->filter(function ($splRequest) use ($user, $splApprovalService) {
                    try {
                        $chain = $splApprovalService->getApprovalChain($splRequest);
                        $userId = $user->id;

                        foreach ($chain as $level => $approverData) {
                            $users = $approverData['users'] ?? collect();
                            $roleKeyInChain = $approverData['role_key'] ?? null;

                            if ($roleKeyInChain === 'hr' && $users->contains('id', $userId)) {
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
                                    }

                                    if (!$prevApproved) {
                                        $previousLevelsApproved = false;
                                        break;
                                    }
                                }

                                // HR bisa approve jika level sebelumnya sudah di-approve dan belum di-approve HR
                                return $previousLevelsApproved &&
                                    is_null($splRequest->hrd_approved_at) &&
                                    is_null($splRequest->hrd_rejected_at);
                            }
                        }

                        return false;
                    } catch (\Exception $e) {
                        \Log::error('Error checking SPL approval chain for HR: ' . $e->getMessage());
                        return false;
                    }
                });
            }
        } catch (\Exception $e) {
            \Log::error('Error getting SPL pending for HR: ' . $e->getMessage());
        }

        // Cek apakah HRD ada di approval flow untuk vehicle_asset
        $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
        $hrInVehicleFlow = false;

        foreach ($vehicleAssetFlow as $setting) {
            if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                $hrInVehicleFlow = true;
                break;
            }
        }

        $vehicleRequests = collect();
        $assetRequests = collect();

        if ($hrInVehicleFlow) {
            // Get pending vehicle and asset requests untuk HR level
            // HR hanya perlu approve yang statusnya sudah manager_approved
            $vehicleRequests = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                ->where('status', \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED)
                ->orderBy('created_at', 'desc')
                ->get();

            $assetRequests = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                ->where('status', \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('hr.approval.hr-pending', compact('formRequestsWithAccess', 'overtimeRequests', 'vehicleRequests', 'assetRequests', 'approvalInfo', 'splRequests'));
    }

    /**
     * Display manager pending requests (Semua jenis permohonan dalam satu halaman Approval)
     * Mengikuti ApprovalSetting; hanya tampil jika MANAGER ada di flow untuk jenis terkait
     */
    public function managerPending(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        // dd($user);

        // Authorization dasar: hanya MANAGER yang bisa akses
        if ((int) $user->jabatan !== 3) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya MANAGER yang dapat mengakses.');
        }

        // FORMS (absence, shift_change)
        // Get employee IDs dari divisi yang sama dengan Manager
        $employeeIds = User::where('divisi', $user->divisi)->pluck('id');

        // Debug: Log employee IDs dan divisi
        Log::debug('Manager Pending - Manager Divisi: ' . $user->divisi);
        Log::debug('Manager Pending - Employee IDs: ' . $employeeIds->implode(','));

        $formRequests = collect();
        foreach (['shift_change', 'absence'] as $requestType) {
            // Untuk absence, gunakan divisi manager untuk mendapatkan approval flow yang sesuai
            $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
            $flow = ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

            // Untuk absence, cek DivisiApprovalSetting apakah Manager enabled
            $divisiSetting = null;
            if ($requestType === 'absence') {
                $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $user->divisi)
                    ->where('is_active', true)
                    ->first();
            }

            // Pastikan MANAGER memang ada di flow
            // KHUSUS untuk shift_change: Manager bisa approve melalui head_division dengan allowed_jabatan yang mencakup jabatan 3
            // Untuk absence: Manager hanya bisa approve melalui role_key === 'manager' DAN manager_enabled di DivisiApprovalSetting
            $managerSetting = null;

            if ($requestType === 'shift_change') {
                // Untuk shift_change: manager juga bisa membaca role 'head_division'
                foreach ($flow as $setting) {
                    if (
                        $setting->role_key === 'manager' ||
                        ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user))
                    ) {
                        $managerSetting = $setting;
                        break;
                    }
                }
            } else {
                // Untuk absence: hanya membaca role 'manager' dengan pengecekan DivisiApprovalSetting
                foreach ($flow as $setting) {
                    if ($setting->role_key === 'manager') {
                        // Pastikan Manager enabled di DivisiApprovalSetting
                        if (!$divisiSetting || !($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                            Log::debug("Manager Pending - Manager disabled in DivisiApprovalSetting for absence");
                            continue; // Manager disabled untuk divisi ini, skip
                        }
                        $managerSetting = $setting;
                        break;
                    }
                }
            }
            if (!$managerSetting) {
                Log::debug("Manager Pending - No manager setting found for {$requestType}");
                continue;
            }

            $order = $managerSetting->approval_order;
            $roleKey = $managerSetting->role_key;

            Log::debug("Manager Pending - Request Type: {$requestType}, Manager Order: {$order}, Role Key: {$roleKey}");

            $query = EmployeeRequest::whereIn('employee_id', $employeeIds)
                ->where('request_type', $requestType);

            // Jika Manager approve melalui head_division (hanya untuk shift_change), cek head_approved_at
            // Jika Manager approve melalui manager, cek manager_approved_at
            if ($roleKey === 'head_division' && $requestType === 'shift_change') {
                $query->whereNull('head_approved_at')
                    ->whereNull('head_rejected_at');
            } else {
                $query->whereNull('manager_approved_at')
                    ->whereNull('manager_rejected_at');
            }

            Log::debug("Manager Pending - Request Type: {$requestType}, Manager Order: {$order}");

            if ($order == 1) {
                $query->whereNull('supervisor_approved_at')
                    ->whereNull('supervisor_rejected_at')
                    ->whereNull('head_approved_at')
                    ->whereNull('head_rejected_at');
            } else {
                // Semua approver sebelum MANAGER harus sudah approved
                // Untuk setiap approver sebelumnya, pastikan sudah approve
                for ($i = 1; $i < $order; $i++) {
                    $prev = $flow->firstWhere('approval_order', $i);
                    if (!$prev) {
                        continue;
                    }

                    Log::debug("Manager Pending - Previous Approver Order {$i}: {$prev->role_key}");

                    if ($prev->role_key === 'spv_division') {
                        // SPV sudah approve: cek supervisor_approved_at DAN status supervisor_approved
                        $query->whereNotNull('supervisor_approved_at')
                            ->whereNull('supervisor_rejected_at')
                            ->where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED);
                    } elseif ($prev->role_key === 'head_division') {
                        // HEAD sudah approve
                        $query->whereNotNull('head_approved_at')
                            ->whereNull('head_rejected_at');
                    }
                }
            }

            $results = $query->get();
            Log::debug("Manager Pending - Found {$results->count()} requests for {$requestType}");
            foreach ($results as $req) {
                Log::debug("Manager Pending - Request ID: {$req->id}, Status: {$req->status}, Employee ID: {$req->employee_id}");
            }

            $formRequests = $formRequests->merge($results);
        }
        // dd($formRequests);

        // OVERTIME: Manager tidak menjadi approver pada flow SPL saat ini → kosongkan
        $overtimeRequests = collect();

        // VEHICLE / ASSET
        $vehicleRequests = VehicleAssetRequest::where('request_type', 'vehicle')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER)
            ->get();

        $assetRequests = VehicleAssetRequest::where('request_type', 'asset')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER)
            ->get();

        // SPL (Surat Perintah Lembur): ambil SPL pending untuk MANAGER
        // MANAGER dan HEAD ada di level yang sama (head_division dengan allowed_jabatan [3, 4])
        // Jadi MANAGER masuk ke chain dengan role_key = 'head_division', bukan 'manager'
        // MANAGER bisa approve SPL dengan status pending, tidak perlu menunggu HEAD
        $splRequests = collect();
        try {
            // Gunakan service yang sudah ada untuk mendapatkan SPL pending untuk MANAGER
            $splApprovalService = new \App\Services\SplApprovalService();
            $splRequests = $splApprovalService::getPendingRequestsForUser($user);

            // Filter lagi: hanya SPL yang MANAGER bisa approve
            // Karena MANAGER masuk ke chain dengan role_key = 'head_division',
            // kita perlu cek apakah user ini MANAGER (jabatan 3) dan apakah SPL belum di-approve
            // Catatan: MANAGER yang approve akan update head_approved_at (karena role_key = 'head_division')
            // tapi kita juga perlu cek manager_approved_at untuk memastikan MANAGER belum approve
            $splRequests = $splRequests->filter(function ($splRequest) use ($user) {
                // MANAGER (jabatan 3) bisa approve jika:
                // 1. Belum di-approve oleh HEAD (head_approved_at masih null)
                // 2. Belum di-reject oleh HEAD (head_rejected_at masih null)
                // 3. Belum di-approve oleh MANAGER (manager_approved_at masih null) - untuk memastikan
                if ((int) $user->jabatan === 3) {
                    return is_null($splRequest->head_approved_at) &&
                        is_null($splRequest->head_rejected_at) &&
                        is_null($splRequest->manager_approved_at) &&
                        is_null($splRequest->manager_rejected_at);
                }
                return false;
            });
        } catch (\Exception $e) {
            \Log::error('Error getting SPL pending for MANAGER: ' . $e->getMessage());
        }

        return view('hr.approval.manager-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests', 'splRequests'));
    }

    /**
     * Display General Manager pending requests
     * Hanya untuk request yang dibuat oleh Manager (jabatan 3) dan memiliki general_id
     * Bisa diakses oleh:
     * - General Manager (divisi 13)
     * - Manager (jabatan 3, bukan divisi 13) - untuk monitoring request dari divisi yang sama
     */
    public function generalManagerPending()
    {
        $user = Auth::user();

        // Authorization: General Manager (divisi 13) atau Manager (jabatan 3, bukan divisi 13)
        if ($user->divisi != 13 && !((int) $user->jabatan === 3 && $user->divisi != 13)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya General Manager atau Manager yang dapat mengakses.');
        }

        // STEP 1: Cari request yang dibuat oleh Manager (jabatan 3) yang general_id belum di-set
        // dan auto-set general_id ke user ini
        // Karena EmployeeRequest menggunakan connection pgsql2 dan User menggunakan pgsql,
        // kita perlu query employee IDs terlebih dahulu
        $managerEmployeeIds = User::on('pgsql')
            ->where('jabatan', 3)
            ->pluck('id');

        $managerCreatedRequests = EmployeeRequest::whereIn('employee_id', $managerEmployeeIds)
            ->whereIn('request_type', ['shift_change', 'absence'])
            ->whereNull('general_id') // Belum di-set general_id
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->where('status', '!=', EmployeeRequest::STATUS_HR_APPROVED) // Belum final approved
            ->get();

        // Auto-set general_id untuk request yang belum di-set
        foreach ($managerCreatedRequests as $request) {
            $request->update(['general_id' => $user->id]);
            Log::info('General Manager Pending - Auto-set general_id for Manager request', [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'request_type' => $request->request_type,
                'employee_id' => $request->employee_id,
                'general_manager_id' => $user->id
            ]);
        }

        // STEP 2: Query request yang memiliki general_id = user.id dan belum di-approve/reject
        $formRequests = EmployeeRequest::where('general_id', $user->id)
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->whereIn('request_type', ['shift_change', 'absence'])
            ->with(['employee', 'employee.divisiUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Map formRequests untuk menambahkan informasi approval
        $formRequests = $formRequests->map(function ($request) use ($user) {
            $request->can_approve = true; // General Manager selalu bisa approve request yang ditunjuk ke dia
            $request->cannot_approve_reason = null;
            return $request;
        });

        // Debug: Log hasil query
        Log::info('General Manager Pending - Form Requests Found', [
            'count' => $formRequests->count(),
            'request_ids' => $formRequests->pluck('id')->toArray(),
            'auto_fixed_count' => $managerCreatedRequests->count()
        ]);

        // STEP 3: Cari Vehicle/Asset request yang dibuat oleh Manager (jabatan 3) yang general_id belum di-set
        // Karena VehicleAssetRequest tidak punya relationship employee, kita perlu query employee IDs terlebih dahulu
        $managerEmployeeIds = User::on('pgsql')
            ->where('jabatan', 3)
            ->pluck('id');

        $managerVehicleRequests = \App\Models\VehicleAssetRequest::whereIn('employee_id', $managerEmployeeIds)
            ->whereNull('general_id')
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->where('request_type', 'vehicle')
            ->get();

        $managerAssetRequests = \App\Models\VehicleAssetRequest::whereIn('employee_id', $managerEmployeeIds)
            ->whereNull('general_id')
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->where('request_type', 'asset')
            ->get();

        // Auto-set general_id untuk vehicle/asset request yang belum di-set
        foreach ($managerVehicleRequests as $request) {
            try {
                $request->update(['general_id' => $user->id]);
            } catch (\Exception $e) {
                // Jika kolom general_id belum ada, skip
                Log::warning('General Manager Pending - Cannot set general_id for vehicle request', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        foreach ($managerAssetRequests as $request) {
            try {
                $request->update(['general_id' => $user->id]);
            } catch (\Exception $e) {
                // Jika kolom general_id belum ada, skip
                Log::warning('General Manager Pending - Cannot set general_id for asset request', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Query Vehicle/Asset requests yang memiliki general_id = user.id
        $vehicleRequests = \App\Models\VehicleAssetRequest::where('general_id', $user->id)
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->where('request_type', 'vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        $assetRequests = \App\Models\VehicleAssetRequest::where('general_id', $user->id)
            ->whereNull('general_approved_at')
            ->whereNull('general_rejected_at')
            ->where('request_type', 'asset')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.approval.general-manager-pending', compact('formRequests', 'vehicleRequests', 'assetRequests'));
    }

    /**
     * Show approval form
     */
    public function showApprovalForm($id)
    {
        $user = Auth::user();

        $request = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head',
            'general',
            'replacementPerson',
            'employee.jabatanUser',
            'employee.divisiUser'
        ])->findOrFail($id);


        // Check if user can approve this request
        // General Manager (divisi 13), Manager (jabatan = 3), HEAD/SPV (canApprove), or HR
        $canApprove = $this->canApproveRequest($user, $request);

        $isGeneralManager = (int) $user->divisi === 13;
        $isGeneralManagerForThisRequest = $isGeneralManager && $request->general_id == $user->id && is_null($request->general_approved_at) && is_null($request->general_rejected_at);

        if (!$canApprove && !$user->isHR() && !$isGeneralManagerForThisRequest) {
            abort(403, 'Anda tidak dapat menyetujui pengajuan ini.');
        }

        // Get approval flow untuk ditampilkan di view (set di awal agar bisa digunakan di bagian absence)
        // Untuk request yang dibuat oleh Manager (jabatan 3), hanya tampilkan General Manager dan HRD
        // CUSTOM: Untuk HEAD PRODUKSI (jabatan 4, divisi 4), juga hanya tampilkan General Manager dan HRD
        $isManager = $request->employee && (int) $request->employee->jabatan === 3;
        $isHeadProduksi = $request->employee && (int) $request->employee->jabatan === 4 && (int) $request->employee->divisi === 4;

        if ($isManager || $isHeadProduksi) {
            // Untuk request dari Manager atau HEAD PRODUKSI, alur approval hanya: General Manager -> HRD
            $approvalFlow = collect([
                (object) [
                    'role_key' => 'general_manager',
                    'approval_order' => 1,
                    'description' => 'General Manager',
                    'is_active' => true,
                ],
                (object) [
                    'role_key' => 'hr',
                    'approval_order' => 2,
                    'description' => 'HRD',
                    'is_active' => true,
                ],
            ]);
        } else {
            // dd('siini');
            // Untuk request dari non-Manager dan non-HEAD PRODUKSI, gunakan approval flow normal
            $requesterDivisi = $request->employee ? $request->employee->divisi : null;
            $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
            // dd($approvalFlow);
        }

        // Get list of employees for replacement person dropdown (only for absence requests and first approval)
        $employees = collect();
        $isFirstApproval = false;

        if ($request->request_type === 'absence') {
            // Check if this is the first approval (HEAD DIVISI)
            // $approvalFlow sudah di-set di atas
            $firstApprover = $approvalFlow->firstWhere('approval_order', 1);

            if ($firstApprover && $firstApprover->role_key === 'head_division' && $user->canApprove()) {
                $currentOrder = $request->current_approval_order ?? 0;
                if ($currentOrder < 1) {
                    $isFirstApproval = true;

                    // Get employees from the same division as requester
                    // Use eager loaded employee relationship instead of querying again
                    $requesterDivisi = $request->employee ? $request->employee->divisi : null;

                    if ($requesterDivisi) {
                        $employees = \App\Models\User::where('divisi', $requesterDivisi)
                            ->where('id', '!=', $request->employee_id) // Exclude requester
                            ->orderBy('name')
                            ->get(['id', 'name']);
                    }
                }
            }
        }

        // Calculate shouldShowReplacementForm
        // Form pelaksana tugas hanya muncul untuk CUTI TAHUNAN saja
        // KECUALI jika pemohon adalah Manager (jabatan 3), maka tidak perlu ngisi
        $isManagerRequester = $request->employee && (int) $request->employee->jabatan === 3;
        $absenceType = $request->request_data['absence_type'] ?? '';
        $isCutiTahunan = strtoupper(trim($absenceType)) === 'CUTI TAHUNAN';

        $shouldShowReplacementForm = $isFirstApproval
            && $request->request_type === 'absence'
            && $isCutiTahunan
            && !$isManagerRequester;

        // Get schedule data for shift change requests
        $scheduleData = null;
        $newShiftData = null;
        $partnerScheduleData = null;
        $exchangeFromShiftData = null; // Shift data untuk Jam Pemohon
        $exchangeToShiftData = null;   // Shift data untuk Jam Pengganti
        $requesterWorkGroupData = null; // Work group data untuk requester
        $partnerWorkGroupData = null;  // Work group data untuk partner

        if ($request->request_type === 'shift_change') {
            $scenarioType = $request->request_data['scenario_type'] ?? null;

            if ($scenarioType === 'self') {
                // Skenario Self: Get current schedule dan new shift untuk requester
                $workGroupData = $request->employee ? $request->employee->getWorkGroupData() : null;
                $requesterWorkGroupData = $workGroupData;

                if ($workGroupData && $workGroupData->{'Kode Group'}) {
                    $kodeGroup = $workGroupData->{'Kode Group'};
                    $requestDate = $request->request_data['date'] ?? null;

                    if ($requestDate) {
                        try {
                            // Get current schedule
                            $scheduleData = DB::connection('mysql7')
                                ->table('jadwal')
                                ->leftJoin('mastershift', 'jadwal.Kode Shift', '=', 'mastershift.Kode Shift')
                                ->where('jadwal.Kode Group', $kodeGroup)
                                ->where('jadwal.Tgl', $requestDate)
                                ->select('jadwal.Tgl', 'jadwal.Kode Group', 'jadwal.Kode Shift', 'jadwal.Keterangan', 'mastershift.Jam In', 'mastershift.Jam Out')
                                ->first();

                            // Get new shift based on "Jam Baru"
                            $newShiftData = $this->findShiftDataFromTime(
                                $request->request_data['new_start_time'] ?? null,
                                $request->request_data['new_end_time'] ?? null
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to fetch schedule data: ' . $e->getMessage());
                        }
                    }
                }
            } elseif ($scenarioType === 'exchange') {
                // Skenario Exchange: Get schedule untuk requester dan partner
                $requestDate = $request->request_data['date'] ?? null;

                if ($requestDate) {
                    try {
                        // Get requester's current schedule
                        $workGroupData = $request->employee ? $request->employee->getWorkGroupData() : null;
                        $requesterWorkGroupData = $workGroupData;
                        if ($workGroupData && $workGroupData->{'Kode Group'}) {
                            $scheduleData = DB::connection('mysql7')
                                ->table('jadwal')
                                ->leftJoin('mastershift', 'jadwal.Kode Shift', '=', 'mastershift.Kode Shift')
                                ->where('jadwal.Kode Group', $workGroupData->{'Kode Group'})
                                ->where('jadwal.Tgl', $requestDate)
                                ->select('jadwal.Tgl', 'jadwal.Kode Group', 'jadwal.Kode Shift', 'jadwal.Keterangan', 'mastershift.Jam In', 'mastershift.Jam Out')
                                ->first();
                        }

                        // Get partner's current schedule
                        $partnerName = $request->request_data['substitute_name'] ?? null;
                        // dd($partnerName);
                        if ($partnerName) {
                            // Cari partner dari tabel masteremployee
                            $partner = DB::connection('mysql7')->table('masteremployee')
                                ->where('Nama', $partnerName)
                                ->first();

                            // dd($partner);
                            if ($partner) {

                                // dd($partnerName);
                                // Ambil work group data dari masteremployee
                                $partnerWorkGroupData = $partner->{'Kode Group'} ?? null;

                                // dd($partnerWorkGroupData);
                                if ($partnerWorkGroupData) {
                                    $partnerScheduleData = DB::connection('mysql7')
                                        ->table('jadwal')
                                        ->leftJoin('mastershift', 'jadwal.Kode Shift', '=', 'mastershift.Kode Shift')
                                        ->where('jadwal.Kode Group', $partnerWorkGroupData)
                                        ->where('jadwal.Tgl', $requestDate)
                                        ->select(
                                            'jadwal.Tgl',
                                            'jadwal.Kode Group',
                                            'jadwal.Kode Shift',
                                            'jadwal.Keterangan',
                                            'mastershift.Jam In',
                                            'mastershift.Jam Out',
                                            DB::raw("'{$partner->Nama}' as employee_name")
                                        )
                                        ->first();
                                }
                            }
                        }

                        // Get shift data untuk Jam Pemohon (exchange_from_shift)
                        $exchangeFromStartTime = $request->request_data['applicant_start_time'] ?? null;
                        $exchangeFromEndTime = $request->request_data['applicant_end_time'] ?? null;
                        // dd($exchangeFromStartTime, $exchangeFromEndTime);
                        if ($exchangeFromStartTime && $exchangeFromEndTime) {
                            $exchangeFromShiftData = $this->findShiftDataFromTime($exchangeFromStartTime, $exchangeFromEndTime);
                        }

                        // Get shift data untuk Jam Pengganti (exchange_to_shift)
                        $exchangeToStartTime = $request->request_data['substitute_start_time'] ?? null;
                        $exchangeToEndTime = $request->request_data['substitute_end_time'] ?? null;
                        if ($exchangeToStartTime && $exchangeToEndTime) {
                            $exchangeToShiftData = $this->findShiftDataFromTime($exchangeToStartTime, $exchangeToEndTime);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to fetch exchange schedule data: ' . $e->getMessage());
                    }
                }
            }
        }

        // Check if user can approve this request
        $canApprove = $this->canApproveRequest($user, $request);
        // dd($canApprove);

        // Build approval history (moved from view to prevent queries in view)
        $approvalHistory = $this->buildApprovalHistory($request);

        // $approvalFlow sudah di-set di awal method

        // Debug: Log approval flow untuk shift_change
        if ($request->request_type === 'shift_change') {
            \Log::debug('=== SHIFT CHANGE APPROVAL FLOW DEBUG ===');
            \Log::debug('Request Type: ' . $request->request_type);
            \Log::debug('Requester Divisi: ' . ($requesterDivisi ?? 'NULL'));
            \Log::debug('Current Approval Order: ' . ($request->current_approval_order ?? 'NULL'));
            \Log::debug('Approval Flow Count: ' . count($approvalFlow));
            foreach ($approvalFlow as $index => $flow) {
                \Log::debug("Flow " . ($index + 1) . ": " . $flow->role_key . " (order: " . $flow->approval_order . ", desc: " . ($flow->description ?? 'N/A') . ", is_active: " . ($flow->is_active ? 'true' : 'false') . ")");
            }
            \Log::debug('========================================');
        }

        // Determine back route based on user role
        $backRoute = 'hr.approval.supervisor-pending';
        if ($user->isHR()) {
            $backRoute = 'hr.approval.hr-pending';
        } elseif ((int) $user->jabatan === 3) {
            $backRoute = 'hr.approval.manager-pending';
        } elseif ((int) $user->divisi === 13) {
            $backRoute = 'hr.approval.general-manager-pending';
        } elseif ($user->canApprove()) {
            $backRoute = 'hr.approval.head-pending';
        }

        return view('hr.approval.show', compact(
            'request',
            'employees',
            'isFirstApproval',
            'shouldShowReplacementForm',
            'scheduleData',
            'newShiftData',
            'partnerScheduleData',
            'exchangeFromShiftData',
            'exchangeToShiftData',
            'canApprove',
            'approvalHistory',
            'approvalFlow',
            'backRoute'
        ));
    }

    /**
     * Process approval
     */
    public function processApproval(Request $request, $id)
    {
        // dd($request->all(), $id);
        Log::debug('=== PROCESS APPROVAL START ===');
        Log::debug('Request ID: ' . $id);
        Log::debug('POST Data: ' . json_encode($request->all()));
        Log::debug('User ID: ' . Auth::id());

        // dd($request->all(), $id);
        $user = Auth::user();
        // Eager load relationships to prevent N+1 queries
        $employeeRequest = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ])->findOrFail($id);

        // dd($employeeRequest);

        // Check if this is first approval for absence request
        // Manager tidak perlu form pengganti untuk semua jenis cuti
        $isFirstApprovalForAbsence = false;
        if ($employeeRequest->request_type === 'absence') {
            // Ambil divisi pemohon untuk mendapatkan approval flow yang sesuai
            $requesterDivisi = $employeeRequest->employee ? $employeeRequest->employee->divisi : null;
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($employeeRequest->request_type, $requesterDivisi);
            $firstApprover = $approvalFlow->firstWhere('approval_order', 1);
            $currentOrder = $employeeRequest->current_approval_order ?? 0;

            // Cek apakah ini approval pertama dan requester bukan manager
            $isManagerRequester = $employeeRequest->employee && (int) $employeeRequest->employee->jabatan === 3;

            if ($firstApprover && $firstApprover->role_key === 'head_division' && $user->canApprove() && $currentOrder < 1 && !$isManagerRequester) {
                $isFirstApprovalForAbsence = true;
            }
        }

        $validationRules = [
            'action' => 'required|in:approve,reject',
            'notes' => 'required_if:action,reject|nullable|string|max:1000',
        ];

        // Add replacement person validation only for first approval of absence request (kecuali manager)
        if ($isFirstApprovalForAbsence) {
            $validationRules['replacement_person_id'] = 'nullable|exists:users,id';
            $validationRules['replacement_person_name'] = 'required_without:replacement_person_id|nullable|string|max:255';
            $validationRules['replacement_person_nip'] = 'nullable|string|max:50';
            $validationRules['replacement_person_position'] = 'nullable|string|max:255';
        }

        $validatedData = $request->validate($validationRules);

        Log::debug('Validation passed, action: ' . $validatedData['action']);

        // dd($validatedData);

        DB::connection('pgsql2')->beginTransaction();

        try {
            if ($validatedData['action'] === 'approve') {
                Log::debug('Calling approveRequest...');
                $replacementData = [
                    'replacement_person_id' => $validatedData['replacement_person_id'] ?? null,
                    'replacement_person_name' => $validatedData['replacement_person_name'] ?? null,
                    'replacement_person_nip' => $validatedData['replacement_person_nip'] ?? null,
                    'replacement_person_position' => $validatedData['replacement_person_position'] ?? null,
                ];
                $this->approveRequest($user, $employeeRequest, $validatedData['notes'] ?? null, $replacementData);
                Log::debug('approveRequest completed');
            } else {
                Log::debug('Calling rejectRequest...');
                $this->rejectRequest($user, $employeeRequest, $validatedData['notes']);
                Log::debug('rejectRequest completed');
            }

            // Send notifications
            // $this->sendApprovalNotifications($employeeRequest, $validatedData['action'], $validatedData['notes'] ?? null);

            DB::connection('pgsql2')->commit();

            $message = $validatedData['action'] === 'approve' ? 'Pengajuan berhasil disetujui.' : 'Pengajuan berhasil ditolak.';

            // Determine redirect route based on user role
            if ($user->isHR()) {
                $redirectRoute = 'hr.approval.hr-pending';
            } elseif ((int) $user->jabatan === 3) {
                $redirectRoute = 'hr.approval.manager-pending';
            } else if ((int) $user->jabatan === 5) {
                $redirectRoute = 'hr.approval.supervisor-pending';
            } else if ((int) $user->jabatan === 4) {
                $redirectRoute = 'hr.approval.head-pending';
            } else {
                $redirectRoute = 'hr.approval.supervisor-pending';
            }

            $redirectUrl = route($redirectRoute);

            // Check if request is AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }

            // For non-AJAX requests, redirect with flash message
            return redirect()->route($redirectRoute)->with('success', $message);
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();

            // Check if request is AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve requests
     */
    public function bulkApprove(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'request_ids' => 'required|array|min:1',
            'request_ids.*' => 'exists:tb_employee_requests,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Eager load relationships to prevent N+1 queries
        $requests = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ])->whereIn('id', $validatedData['request_ids'])->get();
        $approvedCount = 0;
        $errors = [];

        DB::connection('pgsql2')->beginTransaction();

        try {
            foreach ($requests as $employeeRequest) {
                if ($this->canApproveRequest($user, $employeeRequest) && !$user->isHR()) {
                    $this->approveRequest($user, $employeeRequest, $validatedData['notes'] ?? null);
                    $approvedCount++;
                } else {
                    $errors[] = "Tidak dapat menyetujui pengajuan {$employeeRequest->request_number}";
                }
            }

            // Send notifications for all approved requests
            foreach ($requests as $employeeRequest) {
                if ($this->canApproveRequest($user, $employeeRequest)) {
                    $this->sendApprovalNotifications($employeeRequest, 'approve');
                }
            }

            DB::connection('pgsql2')->commit();

            $message = "Berhasil menyetujui {$approvedCount} pengajuan.";
            if (!empty($errors)) {
                $message .= " Error: " . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get approval history
     */
    public function approvalHistory(Request $request)
    {
        $user = Auth::user();
        // Eager load relationships to prevent N+1 queries
        $query = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ]);

        // Filter based on user role
        if ($user->isHR()) {
            $query->whereIn('status', [
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ($user->hasSupervisor()) {
            $query->where('supervisor_id', $user->id)
                ->whereIn('status', [
                    EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    EmployeeRequest::STATUS_SUPERVISOR_REJECTED
                ]);
        } else {
            $query->where('employee_id', $user->id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('hr.approval.history', compact('requests'));
    }

    /**
     * Approve request
     */
    public function approveRequest($user, EmployeeRequest $request, $notes = null, $replacementData = [])
    {
        // dd($request->all(f));
        Log::debug('=== APPROVE REQUEST START ===');
        Log::debug('User ID: ' . $user->id . ', Name: ' . $user->name);
        Log::debug('Request ID: ' . $request->id);
        Log::debug('Current Status: ' . $request->status);
        Log::debug('Current Approval Order: ' . ($request->current_approval_order ?? 0));

        // Get requester divisi for context
        $requesterDivisi = null;
        if ($request->employee_id) {
            $employee = User::find($request->employee_id);
            $requesterDivisi = $employee ? $employee->divisi : null;
        }

        // Get approval flow dengan divisi parameter (untuk absence)
        $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);

        // dd($approvalFlow);
        $currentSetting = null;
        $userRoleKey = null;

        // Untuk ABSENCE, cek juga DivisiApprovalSetting apakah level enabled
        $divisiSetting = null;
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $requesterDivisi) {
            $divisiSetting = DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();
        }

        // Check General Manager approval first (for Manager-created requests)
        // Jika request memiliki general_id, berarti perlu approval General Manager dulu
        if ($request->general_id && $request->general_id == $user->id && $user->divisi == 13) {
            // User adalah General Manager yang ditunjuk untuk approve request ini
            // Update general approval
            $request->update([
                'general_approved_at' => now(),
                'general_notes' => $notes,
                'current_approval_order' => ($request->current_approval_order ?? 0) + 1
            ]);

            Log::info('General Manager approval completed', [
                'request_id' => $request->id,
                'general_manager_id' => $user->id,
                'general_manager_name' => $user->name
            ]);

            // Update request status untuk menunggu HR approval
            $approvalService = new ApprovalService();
            $approvalService->updateRequestStatus($request);

            return; // Approval selesai, keluar dari method
        }

        // Prioritize HR first (because HR is the final approver)
        // Check if user can approve as HR
        // Tapi pastikan General Manager sudah approve dulu jika request memiliki general_id
        if ($user->isHR()) {
            // KHUSUS UNTUK REQUEST DARI MANAGER: Cek apakah General Manager sudah approve
            if ($request->employee && (int) $request->employee->jabatan === 3) {
                // Request dari Manager: harus General Manager approve dulu baru HR bisa approve
                if (!is_null($request->general_approved_at)) {
                    // General Manager sudah approve, HR bisa approve
                    // Untuk request dari Manager, approval flow adalah: General Manager (order 1) -> HRD (order 2)
                    // Cari HR setting di approval flow
                    foreach ($approvalFlow as $setting) {
                        if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                            $currentSetting = $setting;
                            $userRoleKey = 'hr';
                            break;
                        }
                    }
                    // Jika tidak ada HR di approval flow, tetap set sebagai HR karena ini request dari Manager
                    if (!$currentSetting) {
                        // Buat setting dummy untuk HR dengan order 2 (setelah General Manager)
                        $currentSetting = (object) [
                            'role_key' => 'hr',
                            'approval_order' => 2,
                        ];
                        $userRoleKey = 'hr';
                    }
                } else {
                    // General Manager belum approve, HR belum bisa approve
                    throw new \Exception('General Manager belum menyetujui pengajuan ini.');
                }
            } else {
                // Request dari non-Manager: gunakan logika normal
                // Jika request memiliki general_id, pastikan General Manager sudah approve
                if ($request->general_id && is_null($request->general_approved_at)) {
                    throw new \Exception('General Manager belum menyetujui pengajuan ini.');
                }
                foreach ($approvalFlow as $setting) {
                    // Untuk ABSENCE, HR tidak ada flag di DivisiApprovalSetting, selalu enabled
                    if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                        $currentOrder = $request->current_approval_order ?? 0;
                        $hrOrder = $setting->approval_order;

                        // Check if HR can approve (current_approval_order < hrOrder and all previous approvers have approved)
                        if ($currentOrder < $hrOrder) {
                            $allPreviousApproved = true;
                            for ($i = 1; $i < $hrOrder; $i++) {
                                $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                                if ($prevSetting) {
                                    // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                    if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                        $prevRoleKey = $prevSetting->role_key;
                                        $isPrevLevelEnabled = true;

                                        if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                        } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                        } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                        }

                                        // Jika level sebelumnya disabled, skip cek approval
                                        if (!$isPrevLevelEnabled) {
                                            continue;
                                        }
                                    }

                                    if ($currentOrder < $i) {
                                        // Need to check timestamp
                                        if ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                            $allPreviousApproved = false;
                                            break;
                                        } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                            $allPreviousApproved = false;
                                            break;
                                        } elseif ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                            $allPreviousApproved = false;
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($allPreviousApproved) {
                                $currentSetting = $setting;
                                $userRoleKey = 'hr';
                                break;
                            }
                        }
                    }
                }
            }
        }

        // If HR approval is not possible, check Manager
        if (!$currentSetting && (int) $user->jabatan === 3) {
            Log::debug('Checking Manager approval...');
            foreach ($approvalFlow as $setting) {
                Log::debug('Checking setting: ' . $setting->role_key);
                // Untuk ABSENCE, cek apakah MANAGER enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                        Log::debug('Manager disabled, skipping...');
                        continue; // MANAGER disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'manager' && $setting->isUserAllowedToApprove($user, $context)) {
                    Log::debug('Manager found and allowed!');
                    $currentSetting = $setting;
                    $userRoleKey = 'manager';
                    break;
                }
            }
        }

        // dd($currentSetting, $userRoleKey);

        // Check SPV (jabatan 5) - PRIORITAS: Cek SPV dulu sebelum HEAD
        // Karena SPV (jabatan 5) mungkin juga memiliki canApprove() yang return true
        // Jika dicek HEAD dulu, SPV akan masuk ke logika HEAD padahal seharusnya SPV
        if (!$currentSetting && (int) $user->jabatan === 5) {
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah SPV enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
                        continue; // SPV disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'spv_division' && $setting->isUserAllowedToApprove($user, $context)) {
                    $currentSetting = $setting;
                    $userRoleKey = 'spv_division';
                    break;
                }
            }
        }

        // If Manager approval is not possible, check HEAD DIVISI
        // PENTING: Cek HEAD hanya jika bukan SPV (jabatan 5) untuk menghindari konflik
        // SPV (jabatan 5) harus dicek terlebih dahulu sebelum HEAD
        if (!$currentSetting && $user->canApprove() && (int) $user->jabatan !== 5) {
            // dd('masuk sini');
            // dd($approvalFlow);
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah HEAD enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true)) {
                        continue; // HEAD disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];

                // EXCEPTION: Head Divisi Produksi (4) bisa approve request dari Prepress (3)
                $isAllowed = $setting->isUserAllowedToApprove($user, $context);

                // dd($isAllowed, $user->divisi, $requesterDivisi);
                if (!$isAllowed && $user->divisi == 4 && $requesterDivisi == 3) {
                    // Head Produksi boleh approve request dari Prepress
                    $isAllowed = true;
                    Log::debug('EXCEPTION: Head Produksi (4) approving Prepress (3) request');
                }

                if ($setting->role_key === 'head_division' && $isAllowed) {
                    $currentSetting = $setting;
                    // dd($user->jabatan,$userRoleKey);
                    // manager
                    if ((int) $user->jabatan == 3) {
                        // dd('masuk sini');
                        $userRoleKey = 'manager';
                    } else {
                        // dd('masuk sini 2');
                        $userRoleKey = 'head_division';
                    }
                    // dd($userRoleKey);
                    break;
                }
            }
        }

        // dd($currentSetting, $userRoleKey);

        Log::debug('Current Setting: ' . ($currentSetting ? $currentSetting->role_key : 'NULL'));
        Log::debug('User Role Key: ' . ($userRoleKey ?? 'NULL'));

        if (!$currentSetting) {
            Log::debug('ERROR: No current setting found, throwing exception');
            throw new \Exception('Anda tidak dapat menyetujui pengajuan ini.');
        }

        $currentOrder = $currentSetting->approval_order;
        $maxOrder = $approvalFlow->max('approval_order');

        Log::debug('Current Order: ' . $currentOrder . ', Max Order: ' . $maxOrder);

        // dd($currentOrder, $maxOrder, $userRoleKey);
        // Update based on approver type
        if ($userRoleKey === 'manager') {
            Log::debug('Updating manager approval...');
            // Use query builder to ensure update works consistently
            DB::connection('pgsql2')
                ->table('tb_employee_requests')
                ->where('id', $request->id)
                ->update([
                    'manager_id' => $user->id,
                    'manager_notes' => $notes,
                    'manager_approved_at' => now(),
                    'current_approval_order' => $currentOrder,
                ]);

            Log::debug('Manager approval updated successfully');
        } elseif ($userRoleKey === 'spv_division') {
            // dd('masuk sini');
            $updateData = [
                'supervisor_id' => $user->id,
                'supervisor_notes' => $notes,
                'supervisor_approved_at' => now(),
                'current_approval_order' => $currentOrder,
            ];

            // dd($request);
            $request->update($updateData);

            // Refresh model untuk mendapatkan data terbaru
            $request->refresh();

            // Update status menggunakan ApprovalService untuk memastikan status sesuai dengan approval chain
            $approvalService = new ApprovalService();
            $approvalService->updateRequestStatus($request);

            // Status sudah di-update oleh ApprovalService, tidak perlu melanjutkan ke logika berikutnya
            return;
        } elseif ($userRoleKey === 'head_division') {
            $updateData = [
                'head_id' => $user->id,
                'head_notes' => $notes,
                'head_approved_at' => now(),
                'current_approval_order' => $currentOrder,
                'status' => EmployeeRequest::STATUS_HEAD_APPROVED,
            ];

            // Add replacement person data if provided (only for absence requests and first approval)
            if ($request->request_type === 'absence' && $currentOrder == 1 && !empty($replacementData)) {
                if (!empty($replacementData['replacement_person_id'])) {
                    $updateData['replacement_person_id'] = $replacementData['replacement_person_id'];
                    // Auto-fill name from selected user if not provided manually
                    if (empty($replacementData['replacement_person_name'])) {
                        $replacementUser = \App\Models\User::find($replacementData['replacement_person_id']);
                        if ($replacementUser) {
                            $updateData['replacement_person_name'] = $replacementUser->name;
                        }
                    } else {
                        $updateData['replacement_person_name'] = $replacementData['replacement_person_name'];
                    }
                } elseif (!empty($replacementData['replacement_person_name'])) {
                    // Manual input
                    $updateData['replacement_person_name'] = $replacementData['replacement_person_name'];
                }

                if (!empty($replacementData['replacement_person_nip'])) {
                    $updateData['replacement_person_nip'] = $replacementData['replacement_person_nip'];
                }

                if (!empty($replacementData['replacement_person_position'])) {
                    $updateData['replacement_person_position'] = $replacementData['replacement_person_position'];
                }
            }

            $request->update($updateData);
        } elseif ($userRoleKey === 'hr') {
            // Untuk request dari Manager, current_approval_order harus 2 (setelah General Manager order 1)
            // Untuk request dari non-Manager, gunakan currentOrder dari approval flow
            $finalOrder = $currentOrder;
            if ($request->employee && (int) $request->employee->jabatan === 3) {
                // Request dari Manager: approval order adalah 2 (General Manager = 1, HRD = 2)
                $finalOrder = 2;
            }

            $request->update([
                'status' => EmployeeRequest::STATUS_HR_APPROVED,
                'hr_id' => $user->id,
                'hr_notes' => $notes,
                'hr_approved_at' => now(),
                'current_approval_order' => $finalOrder,
            ]);

            // Insert ke Payroll (tabel ijin) untuk absence request
            if ($request->request_type === 'absence') {
                try {
                    // Get nilai checkbox dari request (jika ada)
                    $gajiDibayar = request()->has('gaji_dibayar') && request()->input('gaji_dibayar') == '1' ? 1 : 0;
                    $potongCuti = request()->has('potong_cuti') && request()->input('potong_cuti') == '1' ? 1 : 0;

                    Log::info('Memanggil syncToPayrollAfterHRApproval', [
                        'request_id' => $request->id,
                        'request_number' => $request->request_number,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'gaji_dibayar' => $gajiDibayar,
                        'potong_cuti' => $potongCuti
                    ]);
                    $this->syncToPayrollAfterHRApproval($request, $user, $gajiDibayar, $potongCuti);
                    Log::info('syncToPayrollAfterHRApproval selesai dipanggil', [
                        'request_id' => $request->id,
                        'request_number' => $request->request_number
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't fail the approval
                    Log::error('=== GAGAL MEMANGGIL syncToPayrollAfterHRApproval ===', [
                        'request_id' => $request->id,
                        'request_number' => $request->request_number,
                        'request_type' => $request->request_type,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'error_trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Update jadwal di mysql7 untuk shift change (self, exchange, dan holiday)
            if ($request->request_type === 'shift_change') {
                try {
                    $scenarioType = $request->request_data['scenario_type'] ?? null;

                    if ($scenarioType === 'self') {
                        // Skenario Self: Update jadwal requester saja
                        $this->updateScheduleForShiftChange($request, $request->employee);
                    } elseif ($scenarioType === 'exchange') {
                        // Skenario Exchange: Update jadwal requester DAN partner
                        $this->updateScheduleForExchange($request);
                    } elseif ($scenarioType === 'holiday') {
                        // Skenario Holiday: Insert ke tukarshift dengan shift OFF
                        $this->insertToTukarShift($request);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the approval
                    Log::error('Failed to update jadwal: ' . $e->getMessage(), [
                        'request_id' => $request->id,
                        'request_type' => $request->request_type,
                        'scenario_type' => $scenarioType
                    ]);
                }
            }

            return; // HR is final approver, no need to check next
        }

        // Check if there are more approvers after current one
        $nextOrder = $currentOrder + 1;
        $nextApprover = $approvalFlow->firstWhere('approval_order', $nextOrder);

        // dd($nextApprover, $currentOrder, $maxOrder);

        Log::debug('Next Order: ' . $nextOrder . ', Next Approver: ' . ($nextApprover ? $nextApprover->role_key : 'NULL'));
        Log::debug('Current Order: ' . $currentOrder . ', Max Order: ' . $maxOrder);

        if ($currentOrder >= $maxOrder) {
            // dd('masuk sini');
            // This is the last approver before HR, move to supervisor_approved status for HR to finalize
            Log::debug('Updating status to supervisor_approved (last approver before HR)');
            $request->update([
                'status' => EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
            ]);
        } elseif ($nextApprover) {
            // dd($nextApprover, $userRoleKey);
            // dd($nextApprover, $userRoleKey);
            Log::debug('Next approver role_key: ' . $nextApprover->role_key . ', User role: ' . $userRoleKey);
            // There are more approvers
            if ($nextApprover->role_key === 'manager') {

                // dd($userRoleKey);
                // Next approver is manager, set status to supervisor_approved (SPV/Head approved, waiting for manager)
                if ($userRoleKey === 'head_division' || $userRoleKey === 'spv_division') {
                    if ($user->jabatan === 3) {
                        // dd('masuk sini 3');
                        Log::debug('Updating status to manager_approved (manager approved, HR next)');
                        $request->update([
                            'status' => EmployeeRequest::STATUS_MANAGER_APPROVED,
                        ]);
                    } else {
                        // dd('masuk sini 4');
                        Log::debug('Updating status to supervisor_approved (waiting for manager)');
                        $request->update([
                            'status' => EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                        ]);
                    }
                } else {
                    // Manager approved, check if HR is next
                    Log::debug('Updating status to manager_approved (manager done, HR next)');
                    $request->update([
                        'status' => EmployeeRequest::STATUS_MANAGER_APPROVED,
                    ]);
                }
            } elseif ($nextApprover->role_key === 'hr') {
                // Next approver is HR, set status based on current approver
                if ($userRoleKey === 'manager') {
                    Log::debug('Updating status to manager_approved (manager approved, HR next)');
                    // Use query builder to avoid model cache issues
                    DB::connection('pgsql2')
                        ->table('tb_employee_requests')
                        ->where('id', $request->id)
                        ->update(['status' => EmployeeRequest::STATUS_MANAGER_APPROVED]);
                    Log::debug('Status updated to manager_approved');
                } else if ($userRoleKey === 'head_division') {
                    Log::debug('Updating status to supervisor_approved (head_division or spv_division)');
                    $request->update([
                        'status' => EmployeeRequest::STATUS_HEAD_APPROVED,
                    ]);
                } else if ($userRoleKey === 'spv_division') {
                    Log::debug('Updating status to supervisor_approved (spv_division)');
                    $request->update([
                        'status' => EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    ]);
                }
            } elseif ($nextApprover->role_key === 'head_division') {
                if ($user->jabatan === 3) {
                    Log::debug('Updating status to manager_approved (manager approved, HR next)');
                    $request->update([
                        'status' => EmployeeRequest::STATUS_MANAGER_APPROVED,
                    ]);
                } else {
                    Log::debug('Updating status to supervisor_approved (waiting for manager)');
                    $request->update([
                        'status' => EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    ]);
                }
            } else {
                // Other approvers, keep status as pending
                $request->update([
                    'status' => EmployeeRequest::STATUS_PENDING,
                ]);
            }
        } else {
            // No more approvers, keep status as pending
            $request->update([
                'status' => EmployeeRequest::STATUS_PENDING,
            ]);
        }
    }

    /**
     * Reject request
     */
    public function rejectRequest($user, EmployeeRequest $request, $notes)
    {
        // Get requester divisi for context
        $requesterDivisi = null;
        if ($request->employee_id) {
            $employee = User::find($request->employee_id);
            $requesterDivisi = $employee ? $employee->divisi : null;
        }

        // Get approval flow dengan divisi parameter (untuk absence)
        $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        $currentSetting = null;
        $userRoleKey = null;

        // Untuk ABSENCE, cek juga DivisiApprovalSetting apakah level enabled
        $divisiSetting = null;
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $requesterDivisi) {
            $divisiSetting = DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();
        }

        // Check General Manager rejection first (for Manager-created requests)
        // Jika request memiliki general_id, berarti bisa direject oleh General Manager
        if ($request->general_id && $request->general_id == $user->id && $user->divisi == 13) {
            // User adalah General Manager yang ditunjuk untuk approve request ini
            // Update general rejection
            $request->update([
                'status' => EmployeeRequest::STATUS_SUPERVISOR_REJECTED, // Atau status rejected yang sesuai
                'general_rejected_at' => now(),
                'general_notes' => $notes
            ]);

            Log::info('General Manager rejection completed', [
                'request_id' => $request->id,
                'general_manager_id' => $user->id,
                'general_manager_name' => $user->name
            ]);

            return; // Rejection selesai, keluar dari method
        }

        // Prioritize HR first (because HR is the final approver)
        // Check if user can reject as HR
        // Tapi pastikan General Manager sudah approve dulu jika request memiliki general_id
        if ($user->isHR()) {
            // Jika request memiliki general_id, pastikan General Manager sudah approve sebelum HR bisa reject
            if ($request->general_id && is_null($request->general_approved_at) && is_null($request->general_rejected_at)) {
                throw new \Exception('General Manager belum memproses pengajuan ini.');
            }
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, HR tidak ada flag di DivisiApprovalSetting, selalu enabled
                if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                    $currentOrder = $request->current_approval_order ?? 0;
                    $hrOrder = $setting->approval_order;

                    // Check if HR can reject (current_approval_order < hrOrder and all previous approvers have approved)
                    if ($currentOrder < $hrOrder) {
                        $allPreviousApproved = true;
                        for ($i = 1; $i < $hrOrder; $i++) {
                            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                            if ($prevSetting) {
                                // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                    $prevRoleKey = $prevSetting->role_key;
                                    $isPrevLevelEnabled = true;

                                    if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                    } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                    } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                    }

                                    // Jika level sebelumnya disabled, skip cek approval
                                    if (!$isPrevLevelEnabled) {
                                        continue;
                                    }
                                }

                                if ($currentOrder < $i) {
                                    // Need to check timestamp
                                    if ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    } elseif ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($allPreviousApproved) {
                            $currentSetting = $setting;
                            $userRoleKey = 'hr';
                            break;
                        }
                    }
                }
            }
        }

        // If HR rejection is not possible, check Manager
        if (!$currentSetting && (int) $user->jabatan === 3) {
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah MANAGER enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                        continue; // MANAGER disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'manager' && $setting->isUserAllowedToApprove($user, $context)) {
                    $currentSetting = $setting;
                    $userRoleKey = 'manager';
                    break;
                }
            }
        }

        // If Manager rejection is not possible, check HEAD DIVISI
        if (!$currentSetting && $user->canApprove()) {
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah HEAD enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true)) {
                        continue; // HEAD disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user, $context)) {
                    $currentSetting = $setting;
                    $userRoleKey = 'head_division';
                    break;
                }
            }
        }

        // Check SPV (jabatan 5)
        if (!$currentSetting && (int) $user->jabatan === 5) {
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah SPV enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
                        continue; // SPV disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'spv_division' && $setting->isUserAllowedToApprove($user, $context)) {
                    $currentSetting = $setting;
                    $userRoleKey = 'spv_division';
                    break;
                }
            }
        }

        if (!$currentSetting) {
            throw new \Exception('Anda tidak dapat menolak pengajuan ini.');
        }

        $currentOrder = $currentSetting->approval_order;

        // Update based on approver type
        if ($userRoleKey === 'manager') {
            $request->update([
                'status' => EmployeeRequest::STATUS_MANAGER_REJECTED,
                'manager_id' => $user->id,
                'manager_notes' => $notes,
                'manager_rejected_at' => now(),
                'current_approval_order' => $currentOrder,
            ]);
        } elseif ($userRoleKey === 'spv_division') {
            $request->update([
                'status' => EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                'supervisor_id' => $user->id,
                'supervisor_notes' => $notes,
                'supervisor_rejected_at' => now(),
                'current_approval_order' => $currentOrder,
            ]);
        } elseif ($userRoleKey === 'head_division') {
            $request->update([
                'status' => EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                'head_id' => $user->id,
                'head_notes' => $notes,
                'head_rejected_at' => now(),
                'current_approval_order' => $currentOrder,
            ]);
        } elseif ($userRoleKey === 'hr') {
            $request->update([
                'status' => EmployeeRequest::STATUS_HR_REJECTED,
                'hr_id' => $user->id,
                'hr_notes' => $notes,
                'hr_rejected_at' => now(),
                'current_approval_order' => $currentOrder,
            ]);
        }
    }

    /**
     * Disapprove (cancel/revoke) an approval that was already made
     * Hanya bisa dilakukan jika belum ada approval berikutnya
     * Support untuk EmployeeRequest dan VehicleAssetRequest
     */
    public function disapprove(Request $request, $id)
    {
        $user = Auth::user();
        $requestType = $request->input('request_type', 'employee_request'); // employee_request atau vehicle_asset

        // Handle VehicleAssetRequest
        if ($requestType === 'vehicle_asset') {
            return $this->disapproveVehicleAsset($request, $id);
        }

        // Handle EmployeeRequest
        $employeeRequest = EmployeeRequest::with(['employee'])->findOrFail($id);

        // Determine which approval to revoke based on user role and approval history
        $canDisapprove = false;
        $approvalRole = null;
        $hasNextApproval = false;

        // Cek apakah user adalah yang melakukan approval
        if ($employeeRequest->supervisor_id == $user->id && $employeeRequest->supervisor_approved_at) {
            $approvalRole = 'spv_division';
            // Cek apakah sudah ada approval berikutnya
            $hasNextApproval = $employeeRequest->head_approved_at ||
                $employeeRequest->manager_approved_at ||
                $employeeRequest->hr_approved_at ||
                $employeeRequest->general_approved_at;
            $canDisapprove = !$hasNextApproval;
        } elseif ($employeeRequest->head_id == $user->id && $employeeRequest->head_approved_at) {
            $approvalRole = 'head_division';
            // Cek apakah sudah ada approval berikutnya
            $hasNextApproval = $employeeRequest->manager_approved_at ||
                $employeeRequest->hr_approved_at ||
                $employeeRequest->general_approved_at;
            $canDisapprove = !$hasNextApproval;
        } elseif ($employeeRequest->manager_id == $user->id && $employeeRequest->manager_approved_at) {
            $approvalRole = 'manager';
            // Cek apakah sudah ada approval berikutnya
            $hasNextApproval = $employeeRequest->hr_approved_at ||
                $employeeRequest->general_approved_at;
            $canDisapprove = !$hasNextApproval;
        } elseif ($employeeRequest->general_id == $user->id && $employeeRequest->general_approved_at) {
            $approvalRole = 'general_manager';
            // Cek apakah sudah ada approval berikutnya
            $hasNextApproval = $employeeRequest->hr_approved_at;
            $canDisapprove = !$hasNextApproval;
        } elseif ($user->isHR() && $employeeRequest->hr_approved_at) {
            $approvalRole = 'hr';
            // HR adalah final approver, tidak ada approval berikutnya
            $canDisapprove = true;
        }

        if (!$canDisapprove) {
            if ($hasNextApproval) {
                return back()->with('error', 'Tidak dapat membatalkan approval karena sudah ada approval berikutnya.');
            }
            return back()->with('error', 'Anda tidak dapat membatalkan approval ini.');
        }

        DB::connection('pgsql2')->beginTransaction();
        try {
            // Get approval flow untuk menentukan current_approval_order yang benar
            $requesterDivisi = $employeeRequest->employee ? $employeeRequest->employee->divisi : null;
            $approvalFlow = ApprovalSetting::getApprovalFlow($employeeRequest->request_type, $requesterDivisi);

            // Clear approval fields berdasarkan role
            $updateData = [];
            $newStatus = EmployeeRequest::STATUS_PENDING;
            $newOrder = 0;

            if ($approvalRole === 'spv_division') {
                $updateData = [
                    'supervisor_id' => null,
                    'supervisor_notes' => null,
                    'supervisor_approved_at' => null,
                ];
                // Cari order SPV di approval flow
                $spvSetting = $approvalFlow->firstWhere('role_key', 'spv_division');
                if ($spvSetting) {
                    $newOrder = max(0, $spvSetting->approval_order - 1);
                }
            } elseif ($approvalRole === 'head_division') {
                $updateData = [
                    'head_id' => null,
                    'head_notes' => null,
                    'head_approved_at' => null,
                    'replacement_person_id' => null,
                    'replacement_person_name' => null,
                    'replacement_person_nip' => null,
                    'replacement_person_position' => null,
                ];
                // Cari order HEAD di approval flow
                $headSetting = $approvalFlow->firstWhere('role_key', 'head_division');
                if ($headSetting) {
                    $newOrder = max(0, $headSetting->approval_order - 1);
                }
                // Jika HEAD disapprove, cek apakah ada SPV yang sudah approve
                if ($employeeRequest->supervisor_approved_at) {
                    $newStatus = EmployeeRequest::STATUS_SUPERVISOR_APPROVED;
                }
            } elseif ($approvalRole === 'manager') {
                $updateData = [
                    'manager_id' => null,
                    'manager_notes' => null,
                    'manager_approved_at' => null,
                ];
                // Cari order MANAGER di approval flow
                $managerSetting = $approvalFlow->firstWhere('role_key', 'manager');
                if ($managerSetting) {
                    $newOrder = max(0, $managerSetting->approval_order - 1);
                }
                // Jika MANAGER disapprove, cek apakah ada HEAD/SPV yang sudah approve
                if ($employeeRequest->head_approved_at || $employeeRequest->supervisor_approved_at) {
                    $newStatus = EmployeeRequest::STATUS_SUPERVISOR_APPROVED;
                }
            } elseif ($approvalRole === 'general_manager') {
                $updateData = [
                    'general_id' => null,
                    'general_notes' => null,
                    'general_approved_at' => null,
                ];
                $newOrder = 0; // General Manager adalah order 1, jadi kembali ke 0
            } elseif ($approvalRole === 'hr') {
                $updateData = [
                    'hr_id' => null,
                    'hr_notes' => null,
                    'hr_approved_at' => null,
                ];
                // Cari order HR di approval flow
                $hrSetting = $approvalFlow->firstWhere('role_key', 'hr');
                if ($hrSetting) {
                    $newOrder = max(0, $hrSetting->approval_order - 1);
                }
                // Jika HR disapprove, status kembali ke status sebelum HR
                if ($employeeRequest->manager_approved_at) {
                    $newStatus = EmployeeRequest::STATUS_MANAGER_APPROVED;
                } elseif ($employeeRequest->head_approved_at || $employeeRequest->supervisor_approved_at) {
                    $newStatus = EmployeeRequest::STATUS_SUPERVISOR_APPROVED;
                } elseif ($employeeRequest->general_approved_at) {
                    // Jika ada general_approved_at, berarti request dari Manager
                    $newStatus = EmployeeRequest::STATUS_PENDING; // Kembali ke pending karena General Manager sudah approve
                }
            }

            $updateData['status'] = $newStatus;
            $updateData['current_approval_order'] = $newOrder;

            $employeeRequest->update($updateData);

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Approval berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Disapprove VehicleAssetRequest
     */
    private function disapproveVehicleAsset(Request $request, $id)
    {
        $user = Auth::user();
        $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

        // Determine which approval to revoke based on user role and approval history
        $canDisapprove = false;
        $approvalRole = null;
        $hasNextApproval = false;

        // Cek apakah user adalah yang melakukan approval
        // General Manager (divisi 13) menggunakan general_id dan general_approved_at
        if ((int) $user->divisi === 13 && $vehicleAssetRequest->general_id == $user->id && $vehicleAssetRequest->general_approved_at) {
            $approvalRole = 'general_manager';
            // Cek apakah sudah ada approval berikutnya (HRGA)
            // General Manager bisa disapprove jika:
            // 1. Belum ada HRGA approval (hrga_at masih null)
            // 2. Status masih manager_approved (belum hrga_approved)
            $hasNextApproval = !is_null($vehicleAssetRequest->hrga_at);
            $statusStillManagerApproved = $vehicleAssetRequest->status === VehicleAssetRequest::STATUS_MANAGER_APPROVED;
            $canDisapprove = !$hasNextApproval && $statusStillManagerApproved;
        } elseif ($vehicleAssetRequest->manager_id == $user->id && $vehicleAssetRequest->manager_at) {
            $approvalRole = 'manager';
            // Cek apakah sudah ada approval berikutnya (HRGA atau General Manager)
            $hasNextApproval = !is_null($vehicleAssetRequest->hrga_at) ||
                !is_null($vehicleAssetRequest->general_approved_at);
            $canDisapprove = !$hasNextApproval;
        } elseif ($user->isHR() && $vehicleAssetRequest->hrga_at) {
            $approvalRole = 'hrga';
            // HRGA adalah final approver, tidak ada approval berikutnya
            $canDisapprove = true;
        }

        if (!$canDisapprove) {
            if ($hasNextApproval) {
                return back()->with('error', 'Tidak dapat membatalkan approval karena sudah ada approval berikutnya (HRGA).');
            }
            if ($approvalRole === 'general_manager' && $vehicleAssetRequest->status !== VehicleAssetRequest::STATUS_MANAGER_APPROVED) {
                return back()->with('error', 'Tidak dapat membatalkan approval karena status request sudah berubah.');
            }
            return back()->with('error', 'Anda tidak dapat membatalkan approval ini.');
        }

        DB::connection('pgsql2')->beginTransaction();
        try {
            $updateData = [];
            $newStatus = VehicleAssetRequest::STATUS_PENDING_MANAGER;

            if ($approvalRole === 'general_manager') {
                $updateData = [
                    'general_id' => null,
                    'general_notes' => null,
                    'general_approved_at' => null,
                    'manager_id' => null, // Clear juga manager_id jika di-set untuk backward compatibility
                    'manager_at' => null,
                    'manager_notes' => null,
                ];
                $newStatus = VehicleAssetRequest::STATUS_PENDING_MANAGER;
            } elseif ($approvalRole === 'manager') {
                $updateData = [
                    'manager_id' => null,
                    'manager_notes' => null,
                    'manager_at' => null,
                ];
                $newStatus = VehicleAssetRequest::STATUS_PENDING_MANAGER;
            } elseif ($approvalRole === 'hrga') {
                $updateData = [
                    'hrga_id' => null,
                    'hrga_notes' => null,
                    'hrga_at' => null,
                ];
                // Jika HRGA disapprove, status kembali ke manager_approved jika ada manager/general approval
                if ($vehicleAssetRequest->manager_at || $vehicleAssetRequest->general_approved_at) {
                    $newStatus = VehicleAssetRequest::STATUS_MANAGER_APPROVED;
                } else {
                    $newStatus = VehicleAssetRequest::STATUS_PENDING_MANAGER;
                }
            }

            $updateData['status'] = $newStatus;
            $vehicleAssetRequest->update($updateData);

            DB::connection('pgsql2')->commit();

            // Jika request AJAX, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Approval berhasil dibatalkan.'
                ]);
            }

            // Redirect ke hr/requests untuk vehicle asset
            return redirect()->route('hr.requests.index')->with('success', 'Approval berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();

            // Jika request AJAX, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('hr.requests.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Send approval notifications
     */
    private function sendApprovalNotifications(EmployeeRequest $request, $action, $notes = null)
    {
        $approverName = '';
        $approverRole = '';

        // Determine approver name and role based on the most recent action
        if ($action === 'reject') {
            // For reject, check which rejection timestamp is most recent
            $rejectionTimestamps = [];
            if ($request->head_rejected_at) {
                $rejectionTimestamps['head'] = $request->head_rejected_at;
            }
            if ($request->manager_rejected_at) {
                $rejectionTimestamps['manager'] = $request->manager_rejected_at;
            }
            if ($request->hr_rejected_at) {
                $rejectionTimestamps['hr'] = $request->hr_rejected_at;
            }

            if (!empty($rejectionTimestamps)) {
                // Get the most recent rejection
                $mostRecent = array_search(max($rejectionTimestamps), $rejectionTimestamps);

                if ($mostRecent === 'hr' && $request->hr_id) {
                    $hr = \App\Models\User::find($request->hr_id);
                    $approverName = $hr ? $hr->name : 'HRD';
                    $approverRole = 'HRD';
                } elseif ($mostRecent === 'head' && $request->head_id) {
                    $head = \App\Models\User::find($request->head_id);
                    $approverName = $head ? $head->name : 'HEAD DIVISI';
                    $approverRole = 'HEAD DIVISI';
                } elseif ($mostRecent === 'manager' && $request->manager_id) {
                    $manager = \App\Models\User::find($request->manager_id);
                    $approverName = $manager ? $manager->name : 'MANAGER';
                    $approverRole = 'MANAGER';
                }
            }
        } else {
            // For approve, check which approval timestamp is most recent
            $approvalTimestamps = [];
            if ($request->head_approved_at) {
                $approvalTimestamps['head'] = $request->head_approved_at;
            }
            if ($request->manager_approved_at) {
                $approvalTimestamps['manager'] = $request->manager_approved_at;
            }
            if ($request->hr_approved_at) {
                $approvalTimestamps['hr'] = $request->hr_approved_at;
            }

            if (!empty($approvalTimestamps)) {
                // Get the most recent approval
                $mostRecent = array_search(max($approvalTimestamps), $approvalTimestamps);

                if ($mostRecent === 'hr' && $request->hr_id) {
                    $hr = \App\Models\User::find($request->hr_id);
                    $approverName = $hr ? $hr->name : 'HRD';
                    $approverRole = 'HRD';
                } elseif ($mostRecent === 'head' && $request->head_id) {
                    $head = \App\Models\User::find($request->head_id);
                    $approverName = $head ? $head->name : 'HEAD DIVISI';
                    $approverRole = 'HEAD DIVISI';
                } elseif ($mostRecent === 'manager' && $request->manager_id) {
                    $manager = \App\Models\User::find($request->manager_id);
                    $approverName = $manager ? $manager->name : 'MANAGER';
                    $approverRole = 'MANAGER';
                }
            }
        }

        // Build notification message
        if ($action === 'approve') {
            $title = 'Pengajuan Disetujui';
            $message = "Pengajuan {$request->request_number} ({$request->request_type_label}) telah disetujui oleh {$approverRole}";
            if ($approverName) {
                $message .= " ({$approverName})";
            }
            if ($notes) {
                $message .= ".\n\nCatatan: {$notes}";
            } else {
                $message .= ".";
            }
        } else {
            $title = 'Pengajuan Ditolak';
            $message = "Pengajuan {$request->request_number} ({$request->request_type_label}) telah ditolak oleh {$approverRole}";
            if ($approverName) {
                $message .= " ({$approverName})";
            }
            if ($notes) {
                $message .= ".\n\nAlasan penolakan: {$notes}";
            } else {
                $message .= ".";
            }
            $message .= "\n\nSilakan buat pengajuan baru jika diperlukan.";
        }

        // Notify employee (pemohon)
        $notificationType = $action === 'approve'
            ? 'request_approved'
            : 'request_rejected';

        HrNotification::create([
            'request_id' => $request->id,
            'recipient_id' => $request->employee_id,
            'notification_type' => $notificationType,
            'title' => $title,
            'message' => $message
        ]);

        // If supervisor/HEAD approved, notify HR
        if ($action === 'approve' && ($request->status === EmployeeRequest::STATUS_SUPERVISOR_APPROVED || $request->status === EmployeeRequest::STATUS_PENDING)) {
            // Check if HR is next in approval flow
            // Untuk absence, gunakan divisi parameter
            $requesterDivisi = $request->employee ? $request->employee->divisi : null;
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
            $hrSetting = $approvalFlow->firstWhere('role_key', 'hr');

            if ($hrSetting) {
                // Get HR users: either is_hr = true OR divisi = 7
                $hrUsers = \App\Models\User::where(function ($query) {
                    $query->where('is_hr', true)
                        ->orWhere('divisi', 7);
                })->get();

                foreach ($hrUsers as $hr) {
                    if ($hrSetting->isUserAllowedToApprove($hr)) {
                        HrNotification::create([
                            'request_id' => $request->id,
                            'recipient_id' => $hr->id,
                            'notification_type' => 'hr_approval',
                            'title' => 'Pengajuan Menunggu Approval HR',
                            'message' => "Pengajuan {$request->request_number} ({$request->request_type_label}) telah disetujui atasan dan menunggu approval HR."
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Check if user can approve request
     */
    public function canApproveRequest($user, EmployeeRequest $request)
    {
        // DEBUG: Log awal
        Log::debug('=== CAN APPROVE REQUEST DEBUG ===');
        Log::debug('User ID: ' . $user->id);
        Log::debug('User Name: ' . $user->name);
        Log::debug('User Jabatan: ' . $user->jabatan);
        Log::debug('User Divisi: ' . $user->divisi);
        Log::debug('Request ID: ' . $request->id);
        Log::debug('Request Type: ' . $request->request_type);
        Log::debug('Request Status: ' . $request->status);
        Log::debug('Current Approval Order: ' . ($request->current_approval_order ?? 0));

        // Get requester divisi for context
        $requesterDivisi = null;
        if ($request->employee_id) {
            $employee = User::find($request->employee_id);
            $requesterDivisi = $employee ? $employee->divisi : null;
        }

        Log::debug('Requester Employee ID: ' . $request->employee_id);
        Log::debug('Requester Divisi: ' . ($requesterDivisi ?? 'NULL'));

        // Get approval flow for this request type (untuk absence, gunakan divisi parameter)
        $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        Log::debug('Approval Flow Count: ' . $approvalFlow->count());
        foreach ($approvalFlow as $flow) {
            Log::debug('Flow - role_key: ' . $flow->role_key . ', order: ' . $flow->approval_order);
        }

        // dd($approvalFlow);

        // Untuk ABSENCE, cek juga DivisiApprovalSetting apakah level enabled
        $divisiSetting = null;
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && $requesterDivisi) {
            $divisiSetting = DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();
            Log::debug('DivisiSetting found: ' . ($divisiSetting ? 'YES' : 'NO'));
            if ($divisiSetting) {
                Log::debug('DivisiSetting - spv_enabled: ' . ($divisiSetting->spv_enabled ? 'true' : 'false'));
                Log::debug('DivisiSetting - head_enabled: ' . ($divisiSetting->head_enabled ? 'true' : 'false'));
                Log::debug('DivisiSetting - manager_enabled: ' . ($divisiSetting->manager_enabled ? 'true' : 'false'));
            }
        }

        // Check if request is still in approval process (not rejected, not hr_approved, not cancelled)
        $approvedStatuses = [
            EmployeeRequest::STATUS_PENDING,
            EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
            EmployeeRequest::STATUS_HEAD_APPROVED,
            EmployeeRequest::STATUS_MANAGER_APPROVED,
        ];

        if (!in_array($request->status, $approvedStatuses)) {
            Log::debug('Request is not in approval process anymore, status: ' . $request->status . ', returning false');
            return false;
        }

        $currentOrder = $request->current_approval_order ?? 0;
        Log::debug('Current Order: ' . $currentOrder);
        Log::debug('Request Details for canApproveRequest', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'request_type' => $request->request_type,
            'status' => $request->status,
            'current_approval_order' => $currentOrder,
            'supervisor_approved_at' => $request->supervisor_approved_at,
            'supervisor_rejected_at' => $request->supervisor_rejected_at,
            'head_approved_at' => $request->head_approved_at,
            'manager_approved_at' => $request->manager_approved_at,
            'hr_approved_at' => $request->hr_approved_at,
            'employee_id' => $request->employee_id,
            'employee_divisi' => $requesterDivisi,
        ]);

        // User info
        Log::debug('User ID: ' . $user->id);
        Log::debug('User Name: ' . $user->name);
        Log::debug('User Jabatan: ' . $user->jabatan);
        Log::debug('User Divisi: ' . $user->divisi);

        // Check General Manager first (divisi 13) - untuk request yang dibuat oleh Manager (jabatan 3)
        // General Manager bisa approve jika general_id sesuai dan belum di-approve/reject
        if ((int) $user->divisi === 13) {
            Log::debug('=== CHECKING GENERAL MANAGER (divisi 13) ===');
            if ($request->general_id == $user->id) {
                // General Manager ditunjuk untuk approve request ini
                if (is_null($request->general_approved_at) && is_null($request->general_rejected_at)) {
                    Log::debug('General Manager can approve - general_id matches and not yet approved/rejected');
                    return true;
                } else {
                    Log::debug('General Manager cannot approve - already approved/rejected');
                }
            } else {
                Log::debug('General Manager cannot approve - general_id does not match (request general_id: ' . ($request->general_id ?? 'NULL') . ', user id: ' . $user->id . ')');
            }
        }

        // Prioritize HR first (because HR is the final approver)
        // Check if user can approve as HR
        // PENTING: HR TIDAK MEMANDANG DIVISI - HR bisa approve dari semua divisi
        if ($user->isHR()) {
            // KHUSUS UNTUK REQUEST DARI MANAGER: Cek apakah General Manager sudah approve
            if ($request->employee && (int) $request->employee->jabatan === 3) {
                // Request dari Manager: harus General Manager approve dulu baru HR bisa approve
                if (!is_null($request->general_approved_at)) {
                    // General Manager sudah approve, HR bisa approve
                    Log::debug('HR can approve - General Manager already approved for Manager request');
                    return true;
                } else {
                    // General Manager belum approve, HR belum bisa approve
                    Log::debug('HR cannot approve - General Manager not yet approved for Manager request');
                    return false;
                }
            }

            // KHUSUS UNTUK REQUEST DARI HEAD PRODUKSI (jabatan 4, divisi 4): Cek apakah General Manager sudah approve
            if ($request->employee && (int) $request->employee->jabatan === 4 && (int) $request->employee->divisi === 4) {
                // Request dari HEAD PRODUKSI: harus General Manager approve dulu baru HR bisa approve
                if (!is_null($request->general_approved_at)) {
                    // General Manager sudah approve, HR bisa approve
                    Log::debug('HR can approve - General Manager already approved for HEAD PRODUKSI request');
                    return true;
                } else {
                    // General Manager belum approve, HR belum bisa approve
                    Log::debug('HR cannot approve - General Manager not yet approved for HEAD PRODUKSI request');
                    return false;
                }
            }

            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah level enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    // HR tidak ada flag di DivisiApprovalSetting, selalu enabled
                    // Tapi tetap cek isUserAllowedToApprove
                } else {
                    // Untuk request type lain, cukup cek ApprovalSetting
                }

                if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                    $hrOrder = $setting->approval_order;

                    // Check if HR can approve (current_approval_order < hrOrder and all previous approvers have approved)
                    if ($currentOrder < $hrOrder) {
                        $allPreviousApproved = true;
                        for ($i = 1; $i < $hrOrder; $i++) {
                            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                            if ($prevSetting) {
                                // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                    $prevRoleKey = $prevSetting->role_key;
                                    $isPrevLevelEnabled = true;

                                    if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                    } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                    } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                    }

                                    // Jika level sebelumnya disabled, skip cek approval
                                    if (!$isPrevLevelEnabled) {
                                        continue;
                                    }
                                }

                                if ($currentOrder < $i) {
                                    // Need to check timestamp
                                    if ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    } elseif ($prevSetting->role_key === 'general_manager' && is_null($request->general_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    } elseif ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                        $allPreviousApproved = false;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($allPreviousApproved) {
                            return true;
                        }
                    }
                }
            }
        }

        // If HR approval is not possible, check Manager
        Log::debug('=== CHECKING MANAGER ===');
        Log::debug('User Jabatan === 3: ' . ((int) $user->jabatan === 3 ? 'YES' : 'NO'));
        if ((int) $user->jabatan === 3) {
            Log::debug('User is Manager, checking approval flow...');
            // dd($approvalFlow);
            foreach ($approvalFlow as $setting) {
                // dd($setting);
                Log::debug('Checking setting - role_key: ' . $setting->role_key . ', order: ' . $setting->approval_order);

                // Untuk ABSENCE, cek apakah MANAGER enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    Log::debug('Request type is ABSENCE, checking manager_enabled...');
                    if (!$divisiSetting) {
                        Log::debug('DivisiSetting is NULL, skipping...');
                        continue; // MANAGER disabled untuk divisi ini, skip
                    }
                    if (!($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                        Log::debug('manager_enabled is FALSE, skipping...');
                        continue; // MANAGER disabled untuk divisi ini, skip
                    }
                    Log::debug('manager_enabled is TRUE, continuing...');
                }

                $context = ['requester_divisi' => $requesterDivisi];
                if ($setting->role_key === 'manager' || $setting->role_key === 'head_division') {
                    Log::debug('Found manager setting, checking isUserAllowedToApprove...');
                    $isAllowed = $setting->isUserAllowedToApprove($user, $context);
                    Log::debug('isUserAllowedToApprove: ' . ($isAllowed ? 'YES' : 'NO'));
                    Log::debug('User divisi: ' . $user->divisi . ', Requester divisi: ' . $requesterDivisi);

                    if ($isAllowed) {
                        $userOrder = $setting->approval_order;
                        Log::debug('User Order: ' . $userOrder);
                        Log::debug('Current Order < User Order: ' . $currentOrder . ' < ' . $userOrder . ' = ' . ($currentOrder < $userOrder ? 'YES' : 'NO'));

                        if ($currentOrder < $userOrder) {
                            // Check if all previous approvers have approved
                            for ($i = 1; $i < $userOrder; $i++) {
                                $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                                if ($prevSetting) {
                                    // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                    if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                        $prevRoleKey = $prevSetting->role_key;
                                        $isPrevLevelEnabled = true;

                                        if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                        } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                        } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                            $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                        }

                                        // Jika level sebelumnya disabled, skip cek approval
                                        if (!$isPrevLevelEnabled) {
                                            continue;
                                        }
                                    }

                                    if ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                        return false;
                                    } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                        return false;
                                    } elseif ($prevSetting->role_key === 'hr' && is_null($request->hr_approved_at)) {
                                        return false;
                                    } elseif ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                        return false;
                                    }
                                }
                            }
                            return true;
                        }
                    }
                }
            }
        }

        // If Manager approval is not possible, check HEAD DIVISI (jabatan 4)
        Log::debug('=== CHECKING HEAD (jabatan 4) ===');
        Log::debug('User Jabatan === 4: ' . ((int) $user->jabatan === 4 ? 'YES' : 'NO'));
        if ((int) $user->jabatan === 4) {
            Log::debug('User is HEAD, checking approval flow...');
            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah HEAD enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true)) {
                        Log::debug('HEAD disabled untuk absence di divisi ini, skip');
                        continue; // HEAD disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];

                // EXCEPTION: Head Divisi Produksi (4) bisa approve request dari Prepress (3)
                $isAllowed = $setting->isUserAllowedToApprove($user, $context);
                // dd($isAllowed);
                if (!$isAllowed && $user->divisi == 4) {
                    // dd('masuk exception');
                    // Head Produksi boleh approve request dari Prepress
                    $isAllowed = true;
                    Log::debug('EXCEPTION: Head Produksi (4) approving Prepress (3) request');
                }

                // dd('keluar exception');

                if ($setting->role_key === 'head_division' && $isAllowed) {
                    $userOrder = $setting->approval_order;

                    if ($currentOrder < $userOrder) {
                        // Check if all previous approvers have approved
                        for ($i = 1; $i < $userOrder; $i++) {
                            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                            if ($prevSetting) {
                                // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                    $prevRoleKey = $prevSetting->role_key;
                                    $isPrevLevelEnabled = true;

                                    if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                    } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                    } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                    }

                                    // Jika level sebelumnya disabled, skip cek approval
                                    if (!$isPrevLevelEnabled) {
                                        continue;
                                    }
                                }

                                if ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                    return false;
                                } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                    return false;
                                } elseif ($prevSetting->role_key === 'hr' && is_null($request->hr_approved_at)) {
                                    return false;
                                } elseif ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                    return false;
                                }
                            }
                        }
                        return true;
                    }
                }
            }
        }

        // Check SPV (jabatan 5)
        if ((int) $user->jabatan === 5) {
            Log::debug('=== CHECKING SPV (jabatan 5) ===');
            Log::debug('SPV Check Details', [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'request_type' => $request->request_type,
                'current_order' => $currentOrder,
                'user_divisi' => $user->divisi,
                'requester_divisi' => $requesterDivisi,
                'divisi_setting_exists' => $divisiSetting !== null,
                'spv_enabled' => $divisiSetting ? ($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true) : false,
            ]);

            foreach ($approvalFlow as $setting) {
                // Untuk ABSENCE, cek apakah SPV enabled di DivisiApprovalSetting
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    if (!$divisiSetting || !($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
                        Log::debug('SPV disabled untuk absence di divisi ini, skip');
                        continue; // SPV disabled untuk divisi ini, skip
                    }
                }

                $context = ['requester_divisi' => $requesterDivisi];
                Log::debug('Checking SPV approval - role_key: ' . $setting->role_key . ', userOrder: ' . $setting->approval_order);
                Log::debug('User divisi: ' . $user->divisi . ', Requester divisi: ' . $requesterDivisi);

                if ($setting->role_key === 'spv_division') {
                    $canApprove = $setting->isUserAllowedToApprove($user, $context);
                    Log::debug('isUserAllowedToApprove result: ' . ($canApprove ? 'true' : 'false'));
                    Log::debug('isUserAllowedToApprove Details', [
                        'setting_id' => $setting->id,
                        'role_key' => $setting->role_key,
                        'approval_order' => $setting->approval_order,
                        'canApprove' => $canApprove,
                        'user_id' => $user->id,
                        'user_jabatan' => $user->jabatan,
                        'user_divisi' => $user->divisi,
                        'requester_divisi' => $requesterDivisi,
                    ]);

                    if (!$canApprove) {
                        Log::debug('SPV isUserAllowedToApprove returned false - divisi mungkin tidak match atau kondisi lain');
                        continue; // Skip ke setting berikutnya
                    }

                    $userOrder = $setting->approval_order;
                    Log::debug('Current order: ' . $currentOrder . ', User order: ' . $userOrder);
                    Log::debug('SPV Approval Check', [
                        'current_order' => $currentOrder,
                        'user_order' => $userOrder,
                        'can_approve_condition' => $currentOrder < $userOrder,
                        'is_first_approver' => $userOrder == 1,
                    ]);

                    // SPV bisa approve jika currentOrder < userOrder (belum sampai ke order SPV)
                    if ($currentOrder < $userOrder) {
                        // Jika SPV adalah approver pertama (order 1), langsung bisa approve
                        if ($userOrder == 1) {
                            Log::debug('SPV is first approver (order 1) - can approve, returning true');
                            return true;
                        }

                        // Check if all previous approvers have approved (hanya jika SPV bukan order pertama)
                        $allPreviousApproved = true;
                        for ($i = 1; $i < $userOrder; $i++) {
                            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
                            if ($prevSetting) {
                                // Untuk ABSENCE, cek apakah level sebelumnya enabled di DivisiApprovalSetting
                                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                                    $prevRoleKey = $prevSetting->role_key;
                                    $isPrevLevelEnabled = true;

                                    if ($prevRoleKey === 'spv_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                                    } elseif ($prevRoleKey === 'head_division' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                                    } elseif ($prevRoleKey === 'manager' && $divisiSetting) {
                                        $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                    }

                                    // Jika level sebelumnya disabled, skip cek approval
                                    if (!$isPrevLevelEnabled) {
                                        continue;
                                    }
                                }

                                if ($prevSetting->role_key === 'spv_division' && is_null($request->supervisor_approved_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                } elseif ($prevSetting->role_key === 'head_division' && is_null($request->head_approved_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                } elseif ($prevSetting->role_key === 'manager' && is_null($request->manager_approved_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                } elseif ($prevSetting->role_key === 'hr' && is_null($request->hr_approved_at)) {
                                    $allPreviousApproved = false;
                                    break;
                                }
                            }
                        }

                        if ($allPreviousApproved) {
                            Log::debug('SPV can approve - all previous approvers approved, returning true');
                            return true;
                        } else {
                            Log::debug('SPV cannot approve - previous approvers not yet approved');
                        }
                    } else {
                        Log::debug('SPV cannot approve - currentOrder >= userOrder (currentOrder: ' . $currentOrder . ', userOrder: ' . $userOrder . ')');
                    }
                } else {
                    Log::debug('Setting role_key bukan spv_division: ' . $setting->role_key);
                }
            }
            Log::debug('SPV check selesai - tidak ada yang match, returning false');
            Log::debug('SPV Check Failed - Final Details', [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'request_type' => $request->request_type,
                'current_order' => $currentOrder,
                'user_divisi' => $user->divisi,
                'requester_divisi' => $requesterDivisi,
                'divisi_setting_exists' => $divisiSetting !== null,
                'spv_enabled' => $divisiSetting ? ($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true) : false,
                'approval_flow_count' => $approvalFlow->count(),
                'spv_setting_found' => $approvalFlow->contains(function ($s) {
                    return $s->role_key === 'spv_division';
                }),
            ]);
        }

        // dd('tidak masuk sini');

        return false;
    }

    /**
     * Get approval statistics
     * For HEAD: Count all pending requests in same division berdasarkan head_approved_at NULL
     * Tapi tetap mengacu ke ApprovalSetting untuk menentukan siapa yang berhak approve
     */
    public function getApprovalStats()
    {
        $user = Auth::user();

        // if (
        //     method_exists($user, 'isHR') && !$user->isHR() &&
        //     method_exists($user, 'canApprove') && !$user->canApprove()
        // ) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $stats = [];

        if (method_exists($user, 'isHR') && $user->isHR()) {
            // HR: Count requests waiting for HR approval (hr_approved_at NULL)
            // Tapi harus mengikuti ApprovalSetting: hanya hitung jika HRD ada di approval flow
            $formPending = 0;

            // Cek untuk setiap request type apakah HRD ada di approval flow
            foreach (['shift_change', 'absence'] as $requestType) {
                $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($requestType);

                // Cek apakah HRD ada di approval flow
                $hrInFlow = false;
                $hrApprovalOrder = null;

                foreach ($approvalFlow as $setting) {
                    if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                        $hrInFlow = true;
                        $hrApprovalOrder = $setting->approval_order;
                        break;
                    }
                }

                // Jika HRD tidak ada di approval flow, skip request type ini
                if (!$hrInFlow || !$hrApprovalOrder) {
                    continue;
                }

                // Get employee IDs - HRD biasanya bisa lihat semua divisi
                $employeeIds = \App\Models\User::pluck('id'); // HRD lihat semua

                // Query berdasarkan approval order
                $query = EmployeeRequest::where('request_type', $requestType)
                    ->whereIn('employee_id', $employeeIds)
                    ->whereNull('hr_approved_at')
                    ->whereNull('hr_rejected_at');

                // Cek approval sebelumnya berdasarkan approval order
                if ($hrApprovalOrder == 1) {
                    // HRD di urutan 1: belum ada approval sama sekali
                    $query->whereNull('head_approved_at')
                        ->whereNull('head_rejected_at')
                        ->whereNull('supervisor_approved_at')
                        ->whereNull('supervisor_rejected_at')
                        ->whereNull('manager_approved_at')
                        ->whereNull('manager_rejected_at');
                } elseif ($hrApprovalOrder == 2) {
                    // HRD di urutan 2: sudah ada 1 approval sebelumnya
                    $query->where(function ($q) {
                        $q->whereNotNull('head_approved_at')
                            ->orWhereNotNull('supervisor_approved_at')
                            ->orWhereNotNull('manager_approved_at');
                    });
                } elseif ($hrApprovalOrder == 3) {
                    // HRD di urutan 3: sudah ada 2 approval sebelumnya
                    $query->where(function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->whereNotNull('head_approved_at')
                                ->whereNotNull('supervisor_approved_at');
                        })->orWhere(function ($subQ) {
                            $subQ->whereNotNull('head_approved_at')
                                ->whereNotNull('manager_approved_at');
                        })->orWhere(function ($subQ) {
                            $subQ->whereNotNull('supervisor_approved_at')
                                ->whereNotNull('manager_approved_at');
                        });
                    });
                }

                $formPending += $query->count();
            }

            // Overtime: skip untuk sekarang
            $overtimePending = 0;

            // Cek apakah HRD ada di approval flow untuk vehicle_asset
            $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
            $hrInVehicleFlow = false;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                    $hrInVehicleFlow = true;
                    break;
                }
            }

            $vehiclePending = 0;
            $assetPending = 0;

            if ($hrInVehicleFlow) {
                $allPendingRequests = ApprovalService::getPendingRequestsForUser($user, null);

                $vehiclePending = $allPendingRequests->filter(function ($req) {
                    return $req instanceof \App\Models\VehicleAssetRequest
                        && $req->request_type === 'vehicle'
                        && $req->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED;
                })->count();

                $assetPending = $allPendingRequests->filter(function ($req) {
                    return $req instanceof \App\Models\VehicleAssetRequest
                        && $req->request_type === 'asset'
                        && $req->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED;
                })->count();
            }

            $stats = [
                'pending_hr_approval' => $formPending + $overtimePending + $vehiclePending + $assetPending,
                'approved_this_month' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_APPROVED)
                    ->whereMonth('hr_approved_at', now()->month)
                    ->whereYear('hr_approved_at', now()->year)
                    ->count(),
                'rejected_this_month' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_REJECTED)
                    ->whereMonth('hr_rejected_at', now()->month)
                    ->whereYear('hr_rejected_at', now()->year)
                    ->count()
            ];
        } elseif (method_exists($user, 'canApprove') && $user->canApprove()) {
            // HEAD/SPV/MANAGER: Count requests berdasarkan approval timestamps yang NULL
            // Menggunakan logika yang sama dengan getDashboardStats() di HRRequestController
            $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');
            $formPending = 0;

            foreach (['shift_change', 'absence'] as $requestType) {
                // Untuk absence, gunakan divisi user untuk mendapatkan approval flow yang sesuai
                $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
                $flow = \App\Models\ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

                // Cari role user dan approval order di flow ini
                $userSetting = null;
                foreach ($flow as $setting) {
                    if ($setting->isUserAllowedToApprove($user)) {
                        $userSetting = $setting;
                        break;
                    }
                }

                // Jika user tidak ada di flow untuk request type ini, skip
                if (!$userSetting) {
                    continue;
                }

                $order = $userSetting->approval_order;
                $query = EmployeeRequest::whereIn('employee_id', $employeeIds)
                    ->where('request_type', $requestType);

                // Filter berdasarkan role user
                if ($userSetting->role_key === 'manager') {
                    $query->whereNull('manager_approved_at')
                        ->whereNull('manager_rejected_at');
                } elseif ($userSetting->role_key === 'spv_division') {
                    $query->whereNull('supervisor_approved_at')
                        ->whereNull('supervisor_rejected_at');
                } elseif ($userSetting->role_key === 'head_division') {
                    $query->whereNull('head_approved_at')
                        ->whereNull('head_rejected_at');
                }

                // Cek approval sebelumnya berdasarkan approval order (sama persis dengan getDashboardStats)
                if ($order == 1) {
                    // User adalah approver pertama, semua level sebelum user harus NULL
                    $query->whereNull('supervisor_approved_at')
                        ->whereNull('supervisor_rejected_at')
                        ->whereNull('head_approved_at')
                        ->whereNull('head_rejected_at');
                } else {
                    // Semua approver sebelum user harus sudah approved
                    $query->where(function ($q) use ($flow, $order) {
                        for ($i = 1; $i < $order; $i++) {
                            $prev = $flow->firstWhere('approval_order', $i);
                            if (!$prev) {
                                continue;
                            }
                            if ($prev->role_key === 'spv_division') {
                                $q->whereNotNull('supervisor_approved_at')
                                    ->whereNull('supervisor_rejected_at');
                            } elseif ($prev->role_key === 'head_division') {
                                $q->whereNotNull('head_approved_at')
                                    ->whereNull('head_rejected_at');
                            } elseif ($prev->role_key === 'manager') {
                                $q->whereNotNull('manager_approved_at')
                                    ->whereNull('manager_rejected_at');
                            }
                        }
                    });
                }

                $formPending += $query->count();
            }

            // Overtime: skip untuk sekarang, sistemnya nanti sendiri
            $overtimePending = 0;

            // Count Vehicle/Asset: manager_at NULL dan status pending_manager (untuk VehicleAssetRequest)
            // Hanya untuk manager
            $vehiclePending = 0;
            $assetPending = 0;

            // Cek apakah user adalah manager
            $isManager = false;
            $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence', $user->divisi);
            $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');

            foreach ($absenceFlow as $setting) {
                if ($setting->role_key === 'manager' && $setting->isUserAllowedToApprove($user)) {
                    $isManager = true;
                    break;
                }
            }

            if (!$isManager) {
                foreach ($shiftChangeFlow as $setting) {
                    if ($setting->role_key === 'manager' && $setting->isUserAllowedToApprove($user)) {
                        $isManager = true;
                        break;
                    }
                }
            }

            if ($isManager) {
                $vehiclePending = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->count();

                $assetPending = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->count();
            }

            $stats = [
                'pending_approval' => $formPending + $overtimePending + $vehiclePending + $assetPending,
                'approved_this_month' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_APPROVED)
                    ->whereMonth('hr_approved_at', now()->month)
                    ->whereYear('hr_approved_at', now()->year)
                    ->count(),
                'rejected_this_month' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_REJECTED)
                    ->whereMonth('hr_rejected_at', now()->month)
                    ->whereYear('hr_rejected_at', now()->year)
                    ->count()
            ];
        }

        return response()->json($stats);
    }

    /**
     * Update jadwal untuk skenario Self shift change
     */
    private function updateScheduleForShiftChange($request, $employee)
    {
        $workGroupData = $employee ? $employee->getWorkGroupData() : null;

        if ($workGroupData && $workGroupData->{'Kode Group'}) {
            $kodeGroup = $workGroupData->{'Kode Group'};
            $requestDate = $request->request_data['date'] ?? null;

            if ($requestDate) {
                $newShiftKode = $this->findShiftCodeFromTime(
                    $request->request_data['new_start_time'] ?? null,
                    $request->request_data['new_end_time'] ?? null
                );

                if ($newShiftKode) {

                    $dataEmployee = DB::connection('mysql7')
                        ->table('masteremployee')
                        ->where('Nama', $employee->name)
                        ->where('Endda', '>=', now())
                        ->first();

                    // dd($requestDate, $newShiftKode, $dataEmployee);

                    if (!$dataEmployee) {
                        \Log::error("Employee not found in masteremployee", [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'request_id' => $request->id
                        ]);
                        return; // Exit early if employee not found
                    }

                    $kodeGroupUpdate = $dataEmployee->{'Kode Group'};

                    if (!$kodeGroupUpdate) {
                        \Log::error("Kode Group not found for employee", [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'request_id' => $request->id
                        ]);
                        return; // Exit early if Kode Group not found
                    }

                    // dd($kodeGroupUpdate);

                    $jadwalUpdate = DB::connection('mysql7')
                        ->table('jadwal')
                        ->where('Kode Group', $kodeGroupUpdate)
                        ->where('Tgl', $requestDate)
                        ->first();

                    // dd($jadwalUpdate);

                    // Log untuk debugging
                    \Log::info("Jadwal query result", [
                        'employee_id' => $employee->id,
                        'kode_group' => $kodeGroupUpdate,
                        'tanggal' => $requestDate,
                        'tanggal_type' => gettype($requestDate),
                        'jadwal_found' => $jadwalUpdate ? 'yes' : 'no',
                        'jadwal_data' => $jadwalUpdate ? [
                            'Kode Group' => $jadwalUpdate->{'Kode Group'} ?? null,
                            'Tgl' => $jadwalUpdate->Tgl ?? null,
                            'Tgl_type' => isset($jadwalUpdate->Tgl) ? gettype($jadwalUpdate->Tgl) : null,
                            'Kode Shift' => $jadwalUpdate->{'Kode Shift'} ?? null,
                        ] : null
                    ]);

                    $kodeShiftUpdate = $jadwalUpdate ? $jadwalUpdate->{'Kode Shift'} : null;

                    // dd($kodeGroupUpdate, $newShiftKode, $requestDate);


                    // if not record, insert new record
                    if ($jadwalUpdate == null) {
                        // dd('ga ada');
                        try {
                            $insertResult = DB::connection('mysql7')
                                ->table('jadwal')
                                ->insert([
                                    'Kode Group' => $kodeGroupUpdate,
                                    'Tgl' => $requestDate,
                                    'Kode Shift' => $newShiftKode
                                ]);

                            \Log::info("Jadwal inserted for self scenario", [
                                'employee_id' => $employee->id,
                                'kode_group' => $kodeGroupUpdate,
                                'tanggal' => $formattedDate,
                                'original_tanggal' => $requestDate,
                                'new_shift' => $newShiftKode,
                                'request_id' => $request->id,
                                'insert_result' => $insertResult
                            ]);

                            // Verify insert by querying back
                            $verifyInsert = DB::connection('mysql7')
                                ->table('jadwal')
                                ->where('Kode Group', $kodeGroupUpdate)
                                ->whereRaw('DATE(Tgl) = ?', [$formattedDate])
                                ->first();

                            if (!$verifyInsert) {
                                \Log::error("Jadwal insert verification failed", [
                                    'employee_id' => $employee->id,
                                    'kode_group' => $kodeGroupUpdate,
                                    'tanggal' => $requestDate,
                                    'new_shift' => $newShiftKode,
                                    'request_id' => $request->id
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to insert jadwal", [
                                'employee_id' => $employee->id,
                                'kode_group' => $kodeGroupUpdate,
                                'tanggal' => $requestDate,
                                'new_shift' => $newShiftKode,
                                'request_id' => $request->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e;
                        }
                    } else {
                        // dd('ada');
                        try {
                            // Pastikan format tanggal konsisten (gunakan DATE() untuk memastikan format sama)
                            // Coba dengan raw query untuk memastikan format tanggal benar
                            $updateResult = DB::connection('mysql7')
                                ->table('jadwal')
                                ->where('Kode Group', $kodeGroupUpdate)
                                ->whereRaw('DATE(Tgl) = ?', [$requestDate])
                                ->update(['Kode Shift' => $newShiftKode]);

                            // Jika masih 0, coba dengan format yang berbeda
                            if ($updateResult == 0) {
                                // Coba dengan format tanggal yang berbeda
                                $updateResult = DB::connection('mysql7')
                                    ->table('jadwal')
                                    ->where('Kode Group', $kodeGroupUpdate)
                                    ->where('Tgl', $requestDate)
                                    ->update(['Kode Shift' => $newShiftKode]);

                                // Jika masih 0, coba dengan casting tanggal
                                if ($updateResult == 0) {
                                    $updateResult = DB::connection('mysql7')
                                        ->table('jadwal')
                                        ->where('Kode Group', $kodeGroupUpdate)
                                        ->whereRaw('CAST(Tgl AS DATE) = ?', [$requestDate])
                                        ->update(['Kode Shift' => $newShiftKode]);
                                }
                            }

                            \Log::info("Jadwal updated for self scenario", [
                                'employee_id' => $employee->id,
                                'kode_group' => $kodeGroupUpdate,
                                'tanggal' => $requestDate,
                                'new_shift' => $newShiftKode,
                                'request_id' => $request->id,
                                'update_result' => $updateResult,
                                'affected_rows' => $updateResult
                            ]);

                            // Verify update by querying back
                            $verifyUpdate = DB::connection('mysql7')
                                ->table('jadwal')
                                ->where('Kode Group', $kodeGroupUpdate)
                                ->where('Tgl', $requestDate)
                                ->where('Kode Shift', $newShiftKode)
                                ->first();

                            if (!$verifyUpdate) {
                                \Log::error("Jadwal update verification failed", [
                                    'employee_id' => $employee->id,
                                    'kode_group' => $kodeGroupUpdate,
                                    'tanggal' => $requestDate,
                                    'new_shift' => $newShiftKode,
                                    'request_id' => $request->id
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to update jadwal", [
                                'employee_id' => $employee->id,
                                'kode_group' => $kodeGroupUpdate,
                                'tanggal' => $requestDate,
                                'new_shift' => $newShiftKode,
                                'request_id' => $request->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e;
                        }
                    }
                }
            }
        }
    }

    /**
     * Update jadwal untuk skenario Exchange shift change
     */
    private function updateScheduleForExchange($request)
    {
        $requestDate = $request->request_data['date'] ?? null;
        if (!$requestDate) {
            return;
        }

        // 1. Update jadwal requester (pemohon) - ambil shift dari Jam Pemohon
        $requester = $request->employee;
        if ($requester) {
            $workGroupData = $requester->getWorkGroupData();

            if ($workGroupData && $workGroupData->{'Kode Group'}) {
                // Requester mengajukan Jam Pemohon
                $applicantStartTime = $request->request_data['applicant_start_time'] ?? null;
                $applicantEndTime = $request->request_data['applicant_end_time'] ?? null;

                // Cari kode shift dari jam pemohon
                $exchangeFromShiftData = $this->findShiftDataFromTime($applicantStartTime, $applicantEndTime);
                // dd($exchangeFromShiftData);

                if ($exchangeFromShiftData && $exchangeFromShiftData->{'Kode Shift'}) {
                    DB::connection('mysql7')
                        ->table('jadwal')
                        ->where('Kode Group', $workGroupData->{'Kode Group'})
                        ->where('Tgl', $requestDate)
                        ->update(['Kode Shift' => $exchangeFromShiftData->{'Kode Shift'}]);

                    \Log::info("Jadwal updated for requester in exchange", [
                        'employee_id' => $requester->id,
                        'employee_name' => $requester->name,
                        'kode_group' => $workGroupData->{'Kode Group'},
                        'tanggal' => $requestDate,
                        'jam_pemohon' => $applicantStartTime . ' - ' . $applicantEndTime,
                        'new_shift' => $exchangeFromShiftData->{'Kode Shift'},
                        'request_id' => $request->id
                    ]);
                } else {
                    \Log::warning("Cannot update requester schedule - shift not found for time", [
                        'employee_id' => $requester->id,
                        'applicant_start_time' => $applicantStartTime,
                        'applicant_end_time' => $applicantEndTime
                    ]);
                }
            }
        }

        // 2. Update jadwal partner (pengganti) - ambil shift dari Jam Pengganti
        $partnerName = $request->request_data['substitute_name'] ?? null;

        // dd($partnerName);
        if ($partnerName) {
            \Log::info("Processing partner schedule update", [
                'partner_name' => $partnerName,
                'request_id' => $request->id
            ]);

            // Cari partner dari masteremployee berdasarkan nama yang masih aktif (Endda >= hari ini)
            $today = now()->format('Y-m-d');
            $partnerEmployee = DB::connection('mysql7')->table('masteremployee')
                ->where('Nama', $partnerName)
                ->whereDate('Endda', '>=', $today)
                ->first();

            // dd($partnerEmployee);

            if ($partnerEmployee) {
                // Ambil work group data partner
                $partnerWorkGroupData = $partnerEmployee;

                // \Log::info("Partner work group data found", [
                //     'partner_name' => $partnerName,
                //     'work_group' => $partnerWorkGroupData ? json_encode($partnerWorkGroupData) : null
                // ]);

                // dd($partnerWorkGroupData);

                if ($partnerWorkGroupData) {
                    // Partner mengajukan Jam Pengganti
                    $substituteStartTime = $request->request_data['substitute_start_time'] ?? null;
                    $substituteEndTime = $request->request_data['substitute_end_time'] ?? null;

                    \Log::info("Finding shift for partner", [
                        'substitute_start_time' => $substituteStartTime,
                        'substitute_end_time' => $substituteEndTime
                    ]);

                    // Cari kode shift dari jam pengganti
                    $exchangeToShiftData = $this->findShiftDataFromTime($substituteStartTime, $substituteEndTime);

                    // dd($exchangeToShiftData);

                    \Log::info("Shift data found for partner", [
                        'shift_data' => $exchangeToShiftData ? json_encode($exchangeToShiftData) : null
                    ]);

                    // dd($requestDate, $partnerWorkGroupData, $exchangeToShiftData);

                    if ($exchangeToShiftData && $exchangeToShiftData->{'Kode Shift'}) {
                        $affectedRows = DB::connection('mysql7')
                            ->table('jadwal')
                            ->where('Kode Group', $partnerWorkGroupData->{'Kode Group'})
                            ->where('Tgl', $requestDate)
                            ->update(['Kode Shift' => $exchangeToShiftData->{'Kode Shift'}]);

                        \Log::info("Jadwal updated for partner in exchange", [
                            'partner_name' => $partnerName,
                            'kode_group' => $partnerWorkGroupData->{'Kode Group'},
                            'tanggal' => $requestDate,
                            'jam_pengganti' => $substituteStartTime . ' - ' . $substituteEndTime,
                            'new_shift' => $exchangeToShiftData->{'Kode Shift'},
                            'affected_rows' => $affectedRows,
                            'request_id' => $request->id
                        ]);
                    } else {
                        \Log::warning("Cannot update partner schedule - shift not found for time", [
                            'partner_name' => $partnerName,
                            'substitute_start_time' => $substituteStartTime,
                            'substitute_end_time' => $substituteEndTime,
                            'exchangeToShiftData' => $exchangeToShiftData
                        ]);
                    }
                } else {
                    \Log::warning("Partner work group not found or invalid", [
                        'partner_name' => $partnerName,
                        'partnerWorkGroupData' => $partnerWorkGroupData
                    ]);
                }
            } else {
                \Log::warning("Partner not found in masteremployee", [
                    'partner_name' => $partnerName
                ]);
            }
        } else {
            \Log::warning("Partner name not provided in request data", [
                'request_id' => $request->id
            ]);
        }
    }

    /**
     * Insert ke tukarshift untuk scenario holiday (Hari Merah/Lembur)
     */
    private function insertToTukarShift($request)
    {
        $holidayWorkDate = $request->request_data['holiday_work_date'] ?? null;
        if (!$holidayWorkDate) {
            \Log::warning("Holiday work date not provided", [
                'request_id' => $request->id
            ]);
            return;
        }

        $requester = $request->employee;
        if (!$requester) {
            \Log::warning("Requester not found", [
                'request_id' => $request->id
            ]);
            return;
        }

        // Ambil NIP dan Kode Group pemohon dari masteremployee
        $today = now()->format('Y-m-d');
        $employeeData = DB::connection('mysql7')->table('masteremployee')
            ->where('Nama', $requester->name)
            ->whereDate('Endda', '>=', $today)
            ->first();

        if (!$employeeData) {
            \Log::warning("Employee not found in masteremployee", [
                'employee_name' => $requester->name,
                'request_id' => $request->id
            ]);
            return;
        }

        $nip = $employeeData->Nip;
        $kodeGroup = $employeeData->{'Kode Group'};
        $shiftCode = 'O'; // Kode shift OFF untuk overtime/holiday work

        try {
            // 1. Insert ke tukarshift
            DB::connection('mysql7')->table('tukarshift')->insertOrIgnore([
                'Tanggal' => $holidayWorkDate,
                'Nip' => $nip,
                'Shift' => $shiftCode
            ]);

            \Log::info("Inserted to tukarshift for holiday work", [
                'employee_name' => $requester->name,
                'nip' => $nip,
                'tanggal' => $holidayWorkDate,
                'shift' => $shiftCode,
                'request_id' => $request->id
            ]);

            // 2. Update jadwal jika ada di tabel jadwal master
            if ($kodeGroup) {
                $affectedRows = DB::connection('mysql7')
                    ->table('jadwal')
                    ->where('Kode Group', $kodeGroup)
                    ->where('Tgl', $holidayWorkDate)
                    ->update(['Kode Shift' => $shiftCode]);

                \Log::info("Updated jadwal for holiday work", [
                    'employee_name' => $requester->name,
                    'nip' => $nip,
                    'kode_group' => $kodeGroup,
                    'tanggal' => $holidayWorkDate,
                    'new_shift' => $shiftCode,
                    'affected_rows' => $affectedRows,
                    'request_id' => $request->id
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to process holiday work: " . $e->getMessage(), [
                'employee_name' => $requester->name,
                'nip' => $nip,
                'kode_group' => $kodeGroup ?? null,
                'tanggal' => $holidayWorkDate,
                'shift' => $shiftCode,
                'request_id' => $request->id
            ]);
        }
    }

    /**
     * Cari kode shift berdasarkan jam (exact match atau closest match)
     */
    private function findShiftCodeFromTime($startTime, $endTime)
    {
        if (!$startTime || !$endTime) {
            return null;
        }

        try {
            $startTimeCarbon = \Carbon\Carbon::parse($startTime);
            $endTimeCarbon = \Carbon\Carbon::parse($endTime);

            $startTimeFormatted = $startTimeCarbon->format('H:i:s');
            $endTimeFormatted = $endTimeCarbon->format('H:i:s');

            // Try exact match first
            $shiftData = DB::connection('mysql7')
                ->table('mastershift')
                ->whereTime('Jam In', '=', $startTimeFormatted)
                ->whereTime('Jam Out', '=', $endTimeFormatted)
                ->select('Kode Shift')
                ->first();

            // If exact match not found, find closest match
            if (!$shiftData) {
                $shiftData = DB::connection('mysql7')
                    ->table('mastershift')
                    ->select(
                        'Kode Shift',
                        DB::raw("ABS(TIMEDIFF(TIME(`Jam In`), '$startTimeFormatted')) + ABS(TIMEDIFF(TIME(`Jam Out`), '$endTimeFormatted')) as total_diff")
                    )
                    ->orderBy('total_diff', 'asc')
                    ->first();
            }

            return $shiftData ? $shiftData->{'Kode Shift'} : null;
        } catch (\Exception $e) {
            \Log::error('Failed to find shift code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari data shift lengkap berdasarkan jam (exact match atau closest match)
     */
    private function findShiftDataFromTime($startTime, $endTime)
    {
        // dd($startTime, $endTime);
        if (!$startTime || !$endTime) {
            return null;
        }

        try {
            $startTimeCarbon = \Carbon\Carbon::parse($startTime);
            $endTimeCarbon = \Carbon\Carbon::parse($endTime);

            $startTimeFormatted = $startTimeCarbon->format('H:i:s');
            $endTimeFormatted = $endTimeCarbon->format('H:i:s');

            // Try exact match first
            $shiftData = DB::connection('mysql7')
                ->table('mastershift')
                ->whereTime('Jam In', '=', $startTimeFormatted)
                ->whereTime('Jam Out', '=', $endTimeFormatted)
                ->select('Kode Shift', 'Nama Shift', 'Jam In', 'Jam Out')
                ->first();

            // If exact match not found, find closest match
            if (!$shiftData) {
                $shiftData = DB::connection('mysql7')
                    ->table('mastershift')
                    ->select(
                        'Kode Shift',
                        'Nama Shift',
                        'Jam In',
                        'Jam Out',
                        DB::raw("ABS(TIMEDIFF(TIME(`Jam In`), '$startTimeFormatted')) + ABS(TIMEDIFF(TIME(`Jam Out`), '$endTimeFormatted')) as total_diff")
                    )
                    ->orderBy('total_diff', 'asc')
                    ->first();
            }

            return $shiftData;
        } catch (\Exception $e) {
            \Log::error('Failed to find shift data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build approval history for display
     * Moved from view to prevent queries in view
     */
    public function buildApprovalHistory(EmployeeRequest $request)
    {
        // Get requester divisi for context
        $requesterDivisi = null;
        if ($request->employee_id) {
            $employee = User::find($request->employee_id);
            $requesterDivisi = $employee ? $employee->divisi : null;
        }

        // Get approval flow untuk menampilkan riwayat secara dinamis
        // Untuk request yang dibuat oleh Manager (jabatan 3) atau HEAD PRODUKSI (jabatan 4, divisi 4), gunakan approval flow khusus
        $isManager = $request->employee && (int) $request->employee->jabatan === 3;
        $isHeadProduksi = $request->employee && (int) $request->employee->jabatan === 4 && (int) $request->employee->divisi === 4;

        if ($isManager || $isHeadProduksi) {
            // Untuk request dari Manager atau HEAD PRODUKSI, alur approval hanya: General Manager -> HRD
            $approvalFlow = collect([
                (object) [
                    'role_key' => 'general_manager',
                    'approval_order' => 1,
                    'description' => 'General Manager',
                    'is_active' => true,
                ],
                (object) [
                    'role_key' => 'hr',
                    'approval_order' => 2,
                    'description' => 'HRD',
                    'is_active' => true,
                ],
            ]);
        } else {
            // Untuk request dari non-Manager dan non-HEAD PRODUKSI, gunakan approval flow normal
            $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        }

        $approvalHistory = [];

        // Add submission history
        $approvalHistory[] = [
            'order' => 0,
            'title' => 'Pengajuan Diajukan',
            'icon' => 'mdi-send',
            'color' => 'primary',
            'status' => 'completed',
            'approver' => $request->employee->name ?? 'N/A',
            'timestamp' => $request->created_at,
            'notes' => null,
        ];

        // Build approval history berdasarkan approval flow
        foreach ($approvalFlow as $setting) {
            $roleKey = $setting->role_key;
            $order = $setting->approval_order;
            $title = '';
            $icon = 'mdi-account-check';
            $approver = null;
            $timestamp = null;
            $notes = null;
            $status = 'pending'; // pending, completed, rejected

            if ($roleKey === 'spv_division') {
                $title = 'Approval SPV';
                if ($request->supervisor_approved_at) {
                    $approver = $request->supervisor->name ?? 'N/A';
                    $timestamp = $request->supervisor_approved_at;
                    $notes = $request->supervisor_notes;
                    $status = 'completed';
                } elseif ($request->supervisor_rejected_at) {
                    $status = 'rejected';
                    $approver = $request->supervisor->name ?? 'N/A';
                    $timestamp = $request->supervisor_rejected_at;
                    $notes = $request->supervisor_notes;
                }
            } elseif ($roleKey === 'head_division') {
                $title = 'Approval HEAD DIVISI';
                if ($request->head_approved_at) {
                    $approver = $request->head->name ?? 'N/A';
                    $timestamp = $request->head_approved_at;
                    $notes = $request->head_notes;
                    $status = 'completed';
                } elseif ($request->head_rejected_at) {
                    $status = 'rejected';
                    $approver = $request->head->name ?? 'N/A';
                    $timestamp = $request->head_rejected_at;
                    $notes = $request->head_notes;
                }
            } elseif ($roleKey === 'manager') {
                $title = 'Approval MANAGER';
                if ($request->manager_approved_at) {
                    $approver = $request->manager->name ?? 'N/A';
                    $timestamp = $request->manager_approved_at;
                    $notes = $request->manager_notes;
                    $status = 'completed';
                } elseif ($request->manager_rejected_at) {
                    $status = 'rejected';
                    $approver = $request->manager->name ?? 'N/A';
                    $timestamp = $request->manager_rejected_at;
                    $notes = $request->manager_notes;
                }
            } elseif ($roleKey === 'general_manager') {
                $title = 'Approval GENERAL MANAGER';
                $icon = 'mdi-account-supervisor';
                if ($request->general_approved_at) {
                    $generalManager = $request->general_id ? User::find($request->general_id) : null;
                    $approver = $generalManager ? $generalManager->name : 'N/A';
                    $timestamp = $request->general_approved_at;
                    $notes = $request->general_notes;
                    $status = 'completed';
                } elseif ($request->general_rejected_at) {
                    $status = 'rejected';
                    $generalManager = $request->general_id ? User::find($request->general_id) : null;
                    $approver = $generalManager ? $generalManager->name : 'N/A';
                    $timestamp = $request->general_rejected_at;
                    $notes = $request->general_notes;
                } elseif ($request->general_id) {
                    // General Manager sudah ditunjuk tapi belum approve/reject
                    $generalManager = User::find($request->general_id);
                    $approver = $generalManager ? $generalManager->name : 'N/A';
                    $status = 'pending';
                }
            } elseif ($roleKey === 'hr') {
                $title = 'Approval HRD';
                $icon = 'mdi-check-all';
                if ($request->hr_approved_at) {
                    $approver = $request->hr->name ?? 'N/A';
                    $timestamp = $request->hr_approved_at;
                    $notes = $request->hr_notes;
                    $status = 'completed';
                } elseif ($request->hr_rejected_at) {
                    $status = 'rejected';
                    $approver = $request->hr->name ?? 'N/A';
                    $timestamp = $request->hr_rejected_at;
                    $notes = $request->hr_notes;
                }
            }

            // Hanya tampilkan jika ada aktivitas atau ini next approver
            if (
                $status !== 'pending' ||
                $order <= ($request->current_approval_order ?? 0) + 1
            ) {
                $approvalHistory[] = [
                    'order' => $order,
                    'title' => $title,
                    'icon' => $icon,
                    'color' => $roleKey === 'hr' ? 'success' : 'info',
                    'status' => $status,
                    'approver' => $approver,
                    'timestamp' => $timestamp,
                    'notes' => $notes,
                ];
            }
        }

        return $approvalHistory;
    }

    /**
     * Sinkronkan data ke Payroll setelah HRD approve (otomatis)
     */
    private function syncToPayrollAfterHRApproval(EmployeeRequest $employeeRequest, $user, $gajiDibayar = 0, $potongCuti = 0)
    {
        Log::info('=== syncToPayrollAfterHRApproval DIPANGGIL ===', [
            'request_id' => $employeeRequest->id,
            'request_number' => $employeeRequest->request_number,
            'request_type' => $employeeRequest->request_type,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'employee_id' => $employeeRequest->employee_id,
            'employee_name' => $employeeRequest->employee->name ?? 'N/A'
        ]);

        // Cek apakah sudah pernah disinkronkan
        try {
            $existingSync = DB::connection('mysql7')
                ->table('ijin')
                ->where('No Referensi', $employeeRequest->request_number)
                ->first();

            if ($existingSync) {
                Log::info('Data already synced to payroll - SKIP', [
                    'request_id' => $employeeRequest->id,
                    'request_number' => $employeeRequest->request_number,
                    'no_ijin' => $existingSync->{'No Ijin'},
                    'existing_sync_date' => $existingSync->{'Tgl Entry'} ?? null
                ]);
                return; // Sudah sinkron, skip
            }
        } catch (\Exception $e) {
            Log::error('Error checking existing sync', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'error_message' => $e->getMessage()
            ]);
            // Continue proses meskipun error cek existing sync
        }

        // Get employee NIP
        $nip = null;
        if ($employeeRequest->employee) {
            // Cek dari masteremployee di payroll
            $payrollEmployee = DB::connection('mysql7')
                ->table('masteremployee')
                ->where('Nama', $employeeRequest->employee->name)
                ->first();

            if ($payrollEmployee) {
                $nip = $payrollEmployee->Nip;
            }
        } else {
            Log::error('Employee not found for payroll sync', [
                'request_id' => $employeeRequest->id
            ]);
            return;
        }

        // Generate No. Ijin (format: I000017847)
        $lastIjin = DB::connection('mysql7')
            ->table('ijin')
            ->where('No Ijin', 'LIKE', 'I%')
            ->orderByRaw('CAST(SUBSTRING(`No Ijin`, 2) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = 1;
        if ($lastIjin && $lastIjin->{'No Ijin'}) {
            $lastNumber = (int) substr($lastIjin->{'No Ijin'}, 1);
            $nextNumber = $lastNumber + 1;
        }

        $noIjin = 'I' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);

        // Get tanggal ijin dari request_data
        $requestData = $employeeRequest->request_data ?? [];
        $tglIjin = null;

        if (isset($requestData['date_start'])) {
            $tglIjin = \Carbon\Carbon::parse($requestData['date_start'])->format('Y-m-d');
        } elseif (isset($requestData['absence_date'])) {
            $tglIjin = \Carbon\Carbon::parse($requestData['absence_date'])->format('Y-m-d');
        } elseif (isset($requestData['date'])) {
            $tglIjin = \Carbon\Carbon::parse($requestData['date'])->format('Y-m-d');
        }

        if (!$tglIjin) {
            Log::error('Tanggal ijin tidak ditemukan', [
                'request_id' => $employeeRequest->id,
                'request_data' => $requestData
            ]);
            return;
        }

        // Mapping Jenis Ijin (sesuai kode di masterijin)
        $jenisIjin = 'I'; // Default: Ijin Dengan Informasi
        $absenceType = $requestData['absence_type'] ?? $requestData['type'] ?? '';

        if (stripos($absenceType, 'sakit') !== false || stripos($absenceType, 'SKD') !== false) {
            $jenisIjin = 'SKD'; // Sakit dengan surat ket. Dokter
        } elseif (stripos($absenceType, 'Cuti Tahunan') !== false || stripos($absenceType, 'tahunan') !== false) {
            $jenisIjin = 'C'; // Cuti Tahunan
        } elseif (stripos($absenceType, 'Cuti Khusus') !== false || stripos($absenceType, 'khusus') !== false) {
            $jenisIjin = 'P1'; // Cuti Khusus
        } elseif (stripos($absenceType, 'Cuti Haid') !== false || stripos($absenceType, 'haid') !== false) {
            $jenisIjin = 'H1'; // Cuti Haid
        } elseif (stripos($absenceType, 'Cuti Hamil') !== false || stripos($absenceType, 'hamil') !== false) {
            $jenisIjin = 'H2'; // Cuti Hamil
        } elseif (stripos($absenceType, 'dinas') !== false) {
            $jenisIjin = 'DIN'; // Dinas
        } elseif (stripos($absenceType, 'Ijin') !== false || stripos($absenceType, 'ijin') !== false) {
            $jenisIjin = 'I'; // Ijin Dengan Informasi (default)
        } else {
            $jenisIjin = 'I'; // Ijin Dengan Informasi (default)
        }

        // Get keterangan
        $keterangan = $requestData['purpose'] ?? $requestData['reason'] ?? $requestData['description'] ?? '';
        if (strlen($keterangan) > 50) {
            $keterangan = substr($keterangan, 0, 47) . '...';
        }

        // Get jam (jika ada)
        $jamIn = null;
        $jamOut = null;
        if (isset($requestData['start_time'])) {
            $jamIn = $requestData['start_time'];
        }
        if (isset($requestData['end_time'])) {
            $jamOut = $requestData['end_time'];
        }

        // Get approval info untuk Approve1 dan Approve2
        $approve1 = null;
        $tglApprove1 = null;
        $approve2 = null;
        $tglApprove2 = null;

        // Approve1: HRD yang approve
        if ($employeeRequest->hr_id && $employeeRequest->hr_approved_at) {
            $hrUser = User::find($employeeRequest->hr_id);

            // approve 1
            $approve1 = $hrUser ? $hrUser->username : 'HRD';
            $tglApprove1 = $employeeRequest->hr_approved_at->format('Y-m-d H:i:s');

            // approve 2
            $approve2 = $hrUser ? $hrUser->username : 'HRD';
            $tglApprove2 = $employeeRequest->hr_approved_at->format('Y-m-d H:i:s');
        }



        // Prepare data untuk insert
        $insertData = [
            'No Ijin' => $noIjin,
            'Nip' => $nip,
            'Tgl Ijin' => $tglIjin,
            'Jenis Ijin' => $jenisIjin,
            'Keterangan' => $keterangan,
            'Jam In' => $jamIn,
            'Jam Out' => $jamOut,
            'Gaji Dibayar' => $gajiDibayar,
            'Potong Cuti' => $potongCuti,
            'No Referensi' => $employeeRequest->request_number,
            'Entry By' => auth()->user()->username,
            'Tgl Entry' => now()->format('Y-m-d H:i:s'),
            'Approve1' => $approve1,
            'Tgl Approve1' => $tglApprove1,
            'Approve2' => $approve2,
            'Tgl Approve2' => $tglApprove2,
        ];

        // Log sebelum insert
        Log::info('=== MULAI SINKRONISASI KE PAYROLL ===', [
            'request_id' => $employeeRequest->id,
            'request_number' => $employeeRequest->request_number,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'gaji_dibayar' => $gajiDibayar,
            'potong_cuti' => $potongCuti,
            'data_to_insert' => $insertData
        ]);

        // if cuti tahunan 
        if ($jenisIjin == 'C') {
            $masterCuti = DB::connection('mysql7')
                ->table('mastercuti')->where('Nip', $nip)
                ->where('Begda', '<=', now()->format('Y-m-d'))
                ->where('Endda', '>=', now()->format('Y-m-d'))
                ->first();
            // dd($masterCuti);
            if ($masterCuti) {
                // update colum  +1
                DB::connection('mysql7')
                    ->table('mastercuti')
                    ->where('Nip', $nip)
                    ->where('Begda', '<=', now()->format('Y-m-d'))
                    ->where('Endda', '>=', now()->format('Y-m-d'))
                    ->update(['Diambil' => $masterCuti->Diambil + 1]);

                // log failed to update mastercuti
                // Log::error('Failed to update mastercuti', [
                //     'nip' => $nip,
                //     'tgl_ijin' => $tglIjin,
                //     'jenis_ijin' => $jenisIjin,
                //     'error_message' => $e->getMessage()
                // ]);
            }
            // else {
            //     // log failed to find mastercuti
            //     Log::error('Failed to find mastercuti', [
            //         'nip' => $nip,
            //         'tgl_ijin' => $tglIjin,
            //         'jenis_ijin' => $jenisIjin
            //     ]);
            // }
        }

        try {


            // Insert ke tabel ijin
            DB::connection('mysql7')->table('ijin')->insert($insertData);


            // dd('masuk sini');

            // Log setelah insert berhasil
            Log::info('=== SINKRONISASI KE PAYROLL BERHASIL ===', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'no_ijin' => $noIjin,
                'nip' => $nip,
                'tgl_ijin' => $tglIjin,
                'jenis_ijin' => $jenisIjin,
                'inserted_at' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Log error detail
            Log::error('=== SINKRONISASI KE PAYROLL GAGAL ===', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'data_attempted' => $insertData
            ]);

            // Re-throw exception agar bisa di-handle di level atas
            throw $e;
        }
    }

    /**
     * Sinkronkan data ke Payroll (tabel ijin) - Manual sync (untuk tombol sinkronisasi)
     */
    public function syncToPayroll($id, Request $request)
    {
        try {
            $user = Auth::user();
            $employeeRequest = EmployeeRequest::with('employee')->findOrFail($id);

            // Validasi: hanya bisa sync jika sudah hr_approved
            if ($employeeRequest->status !== EmployeeRequest::STATUS_HR_APPROVED && is_null($employeeRequest->hr_approved_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan harus sudah disetujui HRD terlebih dahulu sebelum disinkronkan ke Payroll.'
                ], 400);
            }

            // Validasi: hanya untuk absence request
            if ($employeeRequest->request_type !== 'absence') {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinkronisasi ke Payroll hanya untuk pengajuan ketidakhadiran (absence).'
                ], 400);
            }

            // Cek apakah sudah pernah disinkronkan
            $existingSync = DB::connection('mysql7')
                ->table('ijin')
                ->where('No Referensi', $employeeRequest->request_number)
                ->first();

            if ($existingSync) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah pernah disinkronkan ke Payroll dengan No. Ijin: ' . $existingSync->{'No Ijin'}
                ], 400);
            }

            // Get employee NIP
            $nip = null;
            if ($employeeRequest->employee) {
                // Cek dari masteremployee di payroll
                $payrollEmployee = DB::connection('mysql7')
                    ->table('masteremployee')
                    ->where('Nip', $employeeRequest->employee->nip ?? $employeeRequest->employee->id)
                    ->first();

                if ($payrollEmployee) {
                    $nip = $payrollEmployee->Nip;
                } else {
                    // Jika tidak ditemukan, coba cari berdasarkan nama atau gunakan NIP dari employee
                    $nip = $employeeRequest->employee->nip ?? str_pad($employeeRequest->employee->id, 11, '0', STR_PAD_LEFT);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan.'
                ], 400);
            }

            // Generate No. Ijin (format: I000017847)
            $lastIjin = DB::connection('mysql7')
                ->table('ijin')
                ->where('No Ijin', 'LIKE', 'I%')
                ->orderByRaw('CAST(SUBSTRING(`No Ijin`, 2) AS UNSIGNED) DESC')
                ->first();

            $nextNumber = 1;
            if ($lastIjin && $lastIjin->{'No Ijin'}) {
                $lastNumber = (int) substr($lastIjin->{'No Ijin'}, 1);
                $nextNumber = $lastNumber + 1;
            }

            $noIjin = 'I' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);

            // Get tanggal ijin dari request_data
            $requestData = $employeeRequest->request_data ?? [];
            $tglIjin = null;

            if (isset($requestData['date_start'])) {
                $tglIjin = \Carbon\Carbon::parse($requestData['date_start'])->format('Y-m-d');
            } elseif (isset($requestData['absence_date'])) {
                $tglIjin = \Carbon\Carbon::parse($requestData['absence_date'])->format('Y-m-d');
            } elseif (isset($requestData['date'])) {
                $tglIjin = \Carbon\Carbon::parse($requestData['date'])->format('Y-m-d');
            }

            if (!$tglIjin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal ijin tidak ditemukan dalam data pengajuan.'
                ], 400);
            }

            // Mapping Jenis Ijin (sesuai kode di masterijin)
            $jenisIjin = 'I'; // Default: Ijin Dengan Informasi
            $absenceType = $requestData['absence_type'] ?? $requestData['type'] ?? '';

            if (stripos($absenceType, 'sakit') !== false || stripos($absenceType, 'SKD') !== false) {
                $jenisIjin = 'SKD'; // Sakit dengan surat ket. Dokter
            } elseif (stripos($absenceType, 'cuti tahunan') !== false || stripos($absenceType, 'tahunan') !== false) {
                $jenisIjin = 'C'; // Cuti Tahunan
            } elseif (stripos($absenceType, 'cuti khusus') !== false || stripos($absenceType, 'khusus') !== false) {
                $jenisIjin = 'P1'; // Cuti Khusus
            } elseif (stripos($absenceType, 'cuti haid') !== false || stripos($absenceType, 'haid') !== false) {
                $jenisIjin = 'H1'; // Cuti Haid
            } elseif (stripos($absenceType, 'cuti hamil') !== false || stripos($absenceType, 'hamil') !== false) {
                $jenisIjin = 'H2'; // Cuti Hamil
            } elseif (stripos($absenceType, 'dinas') !== false) {
                $jenisIjin = 'DIN'; // Dinas
            } else {
                $jenisIjin = 'I'; // Ijin Dengan Informasi (default)
            }

            // Get keterangan
            $keterangan = $requestData['purpose'] ?? $requestData['reason'] ?? $requestData['description'] ?? '';
            if (strlen($keterangan) > 50) {
                $keterangan = substr($keterangan, 0, 47) . '...';
            }

            // Get jam (jika ada)
            $jamIn = null;
            $jamOut = null;
            if (isset($requestData['start_time'])) {
                $jamIn = $requestData['start_time'];
            }
            if (isset($requestData['end_time'])) {
                $jamOut = $requestData['end_time'];
            }

            // Get approval info untuk Approve1 dan Approve2
            // Approve1: HRD yang approve (hr_id)
            // Approve2: bisa dari general_manager atau manager tergantung flow
            $approve1 = null;
            $tglApprove1 = null;
            $approve2 = null;
            $tglApprove2 = null;

            // Approve1: HRD
            if ($employeeRequest->hr_id && $employeeRequest->hr_approved_at) {
                $hrUser = User::find($employeeRequest->hr_id);
                $approve1 = $hrUser ? $hrUser->name : 'HRD';
                $tglApprove1 = $employeeRequest->hr_approved_at->format('Y-m-d H:i:s');
            }

            // Approve2: General Manager atau Manager (tergantung yang approve)
            if ($employeeRequest->general_id && $employeeRequest->general_approved_at) {
                $generalUser = User::find($employeeRequest->general_id);
                $approve2 = $generalUser ? $generalUser->name : 'General Manager';
                $tglApprove2 = $employeeRequest->general_approved_at->format('Y-m-d H:i:s');
            } elseif ($employeeRequest->manager_id && $employeeRequest->manager_approved_at) {
                $managerUser = User::find($employeeRequest->manager_id);
                $approve2 = $managerUser ? $managerUser->name : 'Manager';
                $tglApprove2 = $employeeRequest->manager_approved_at->format('Y-m-d H:i:s');
            } elseif ($employeeRequest->head_id && $employeeRequest->head_approved_at) {
                $headUser = User::find($employeeRequest->head_id);
                $approve2 = $headUser ? $headUser->name : 'HEAD DIVISI';
                $tglApprove2 = $employeeRequest->head_approved_at->format('Y-m-d H:i:s');
            }

            // Insert ke tabel ijin
            DB::connection('mysql7')->beginTransaction();

            try {
                DB::connection('mysql7')->table('ijin')->insert([
                    'No Ijin' => $noIjin,
                    'Nip' => $nip,
                    'Tgl Ijin' => $tglIjin,
                    'Jenis Ijin' => $jenisIjin,
                    'Keterangan' => $keterangan,
                    'Jam In' => $jamIn,
                    'Jam Out' => $jamOut,
                    'Gaji Dibayar' => 0, // Default false
                    'Potong Cuti' => 0, // Default false
                    'No Referensi' => $employeeRequest->request_number,
                    'Entry By' => $user->name ?? 'System',
                    'Tgl Entry' => now()->format('Y-m-d H:i:s'),
                    'Approve1' => $approve1,
                    'Tgl Approve1' => $tglApprove1,
                    'Approve2' => $approve2,
                    'Tgl Approve2' => $tglApprove2,
                ]);

                // Update field synced_to_payroll_at di employee_request (jika field ada)
                // TODO: Tambahkan migration untuk field ini jika belum ada
                // $employeeRequest->update(['synced_to_payroll_at' => now()]);

                DB::connection('mysql7')->commit();

                Log::info('Data berhasil disinkronkan ke Payroll', [
                    'request_id' => $employeeRequest->id,
                    'request_number' => $employeeRequest->request_number,
                    'no_ijin' => $noIjin,
                    'nip' => $nip
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disinkronkan ke Payroll dengan No. Ijin: ' . $noIjin,
                    'no_ijin' => $noIjin
                ]);
            } catch (\Exception $e) {
                DB::connection('mysql7')->rollBack();
                Log::error('Error sinkronisasi ke Payroll: ' . $e->getMessage(), [
                    'request_id' => $employeeRequest->id,
                    'error' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyinkronkan data ke Payroll: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error dalam syncToPayroll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status sinkronisasi ke Payroll
     */
    public function checkSyncStatus($id, Request $request)
    {
        try {
            $employeeRequest = EmployeeRequest::findOrFail($id);

            // Cek di tabel ijin berdasarkan No Referensi
            $syncData = DB::connection('mysql7')
                ->table('ijin')
                ->where('No Referensi', $employeeRequest->request_number)
                ->first();

            if ($syncData) {
                return response()->json([
                    'success' => true,
                    'synced' => true,
                    'no_ijin' => $syncData->{'No Ijin'},
                    'synced_at' => $syncData->{'Tgl Entry'} ? \Carbon\Carbon::parse($syncData->{'Tgl Entry'})->format('d/m/Y H:i') : null,
                    'message' => 'Data sudah disinkronkan ke Payroll dengan No. Ijin: ' . $syncData->{'No Ijin'}
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'synced' => false,
                    'message' => 'Data belum disinkronkan ke Payroll'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error dalam checkSyncStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
