<?php

namespace App\Services;

use App\Models\EmployeeRequest;
use App\Models\DivisiApprovalSetting;
use App\Models\ApprovalSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * Get approval chain for a request based on division
     * Untuk ABSENCE: ambil flow dari ApprovalSetting, lalu user dari DivisiApprovalSetting (dengan skip level)
     * Untuk SHIFT_CHANGE: ambil flow dari ApprovalSetting saja (tanpa skip level)
     * Untuk request type lain: gunakan DivisiApprovalSetting langsung
     */
    public function getApprovalChain(EmployeeRequest $request)
    {
        // Get division from employee
        $employee = $request->employee;
        if (!$employee) {
            throw new \Exception('Employee tidak ditemukan');
        }

        $divisiId = $employee->divisi;

        if (!$divisiId) {
            throw new \Exception('User tidak memiliki divisi');
        }

        // KHUSUS UNTUK REQUEST DARI MANAGER: Approval chain adalah General Manager -> HRD
        if ((int) $employee->jabatan === 3) {
            return $this->getManagerRequestApprovalChain($request);
        }

        // KHUSUS UNTUK REQUEST DARI HEAD PRODUKSI (jabatan 4, divisi 4): Approval chain adalah General Manager -> HRD
        // Karena Manager Produksi tidak ada, approval langsung ke General Manager
        if ((int) $employee->jabatan === 4 && (int) $employee->divisi === 4) {
            return $this->getHeadProduksiRequestApprovalChain($request);
        }

        // Untuk ABSENCE, gunakan kombinasi ApprovalSetting + DivisiApprovalSetting
        // ApprovalSetting menentukan URUTAN, DivisiApprovalSetting menentukan bagian mana yang di-skip
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
            return $this->getAbsenceApprovalChain($request, $divisiId);
        }

        // Untuk SHIFT_CHANGE, hanya gunakan ApprovalSetting (tidak perlu cek DivisiApprovalSetting untuk skip)
        if ($request->request_type === EmployeeRequest::TYPE_SHIFT_CHANGE) {
            // dd('shift change');
            return $this->getShiftChangeApprovalChain($request, $divisiId);
        }

        // Untuk request type lain, gunakan DivisiApprovalSetting langsung
        $setting = DivisiApprovalSetting::where('divisi_id', $divisiId)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            throw new \Exception('Setting approval untuk divisi ini belum dikonfigurasi');
        }

        return $setting->chain; // Accessor returns ordered chain
    }

    /**
     * Get approval chain untuk ABSENCE
     * PENTING:
     * 1. Ambil URUTAN dari ApprovalSetting (tb_approval_hr_settings) berdasarkan approval_order
     * 2. Cari divisi pemohon di DivisiApprovalSetting untuk mengetahui bagian mana yang di-skip
     * 3. Jika enabled di DivisiApprovalSetting, ambil user approver dari divisi tersebut
     * 4. Jika disabled (0), skip level tersebut
     */
    protected function getAbsenceApprovalChain(EmployeeRequest $request, $divisiId)
    {
        // dd($divisiId);
        // 1. PENTING: Ambil flow dari ApprovalSetting berdasarkan request_type = 'absence'
        //    Untuk absence, gunakan divisi parameter untuk mendapatkan setting yang sesuai
        //    Sudah di-order by approval_order ASC
        //    Catatan: ApprovalSetting::getApprovalFlow() sudah menggunakan DivisiApprovalSetting
        //    dan sudah menangani hardcode divisi 3 -> 4
        $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type, $divisiId);

        // dd($approvalFlow);
        if ($approvalFlow->isEmpty()) {
            throw new \Exception('Setting approval untuk tipe perizinan absence belum dikonfigurasi');
        }

        // 2. PENTING: Ambil DivisiApprovalSetting untuk mengetahui bagian mana yang di-skip
        //    Cari berdasarkan divisi_id pemohon
        //    Catatan: Untuk divisi 3 (PREPRESS), ApprovalSetting::getApprovalFlow() menggunakan setting divisi 4
        //    Tapi untuk query user, kita tetap menggunakan divisi asli (3) karena user ada di divisi 3
        $originalDivisiId = $divisiId;
        
        // HARDCODE: PREPRESS (3) menggunakan alur yang sama dengan PRODUKSI (4) untuk approval flow
        // Tapi untuk query user, tetap gunakan divisi asli
        if ($divisiId === 3) {
            $divisiId = 4; // Gunakan setting divisi 4 untuk approval flow
        }
        
        $divisiSetting = DivisiApprovalSetting::where('divisi_id', $divisiId)
            ->where('is_active', true)
            ->first();

        if (!$divisiSetting) {
            throw new \Exception('Setting approval untuk divisi ini belum dikonfigurasi');
        }
        
        // Untuk query user, gunakan divisi asli (bukan yang di-hardcode)
        $divisiIdForUserQuery = $originalDivisiId;

        // 3. OPTIMIZATION: Batch query untuk semua user approvers sekaligus
        //    Kumpulkan semua kriteria query terlebih dahulu, lalu query sekali
        $userQueries = [];
        $userBasedIds = [];

        foreach ($approvalFlow as $setting) {
            $roleKey = $setting->role_key;

            if ($roleKey === 'spv_division' && ($divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true)) {
                $userQueries[] = ['divisi' => $divisiIdForUserQuery, 'jabatan' => 5, 'role' => 'spv_division'];
            } elseif ($roleKey === 'head_division' && ($divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true)) {
                $userQueries[] = ['divisi' => $divisiIdForUserQuery, 'jabatan' => 4, 'role' => 'head_division'];
            } elseif ($roleKey === 'manager' && ($divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true)) {
                $userQueries[] = ['divisi' => $divisiIdForUserQuery, 'jabatan' => 3, 'role' => 'manager'];
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
        // Gunakan divisi asli untuk matching user (bukan yang di-hardcode untuk setting)
        $usersByRole = [];
        foreach ($allUsers as $user) {
            // Determine role based on jabatan and divisi
            // Gunakan divisi asli untuk matching (bukan yang di-hardcode untuk setting)
            $role = null;
            if ($user->jabatan == 5 && $user->divisi == $divisiIdForUserQuery) {
                $role = 'spv_division';
            } elseif ($user->jabatan == 4 && $user->divisi == $divisiIdForUserQuery) {
                $role = 'head_division';
            } elseif ($user->jabatan == 3 && $user->divisi == $divisiIdForUserQuery) {
                $role = 'manager';
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

        // 4. Build chain dengan URUTAN mengikuti approval_order dari ApprovalSetting
        //    Catatan: ApprovalSetting::getApprovalFlow() sudah menggunakan DivisiApprovalSetting
        //    jadi flow yang dikembalikan sudah sesuai dengan enabled/disabled level
        //    Tapi kita tetap perlu cek lagi untuk memastikan user ada
        $chain = [];

        foreach ($approvalFlow as $setting) {
            // dd($setting);
            // Ambil data dari ApprovalSetting
            $roleKey = $setting->role_key;
            $approvalOrder = $setting->approval_order; // URUTAN dari approval_hr_setting
            $levelName = $setting->approval_level ?? $roleKey;
            $isEnabled = false;

            // 4. Catatan: ApprovalSetting::getApprovalFlow() sudah menggunakan DivisiApprovalSetting
            //    jadi flow yang dikembalikan sudah sesuai dengan enabled/disabled level
            //    Kita hanya perlu mengambil user dari pre-loaded users berdasarkan role
            //    KECUALIKAN jabatan 7 (KARYAWAN)
            $users = collect();

            if ($roleKey === 'spv_division') {
                // ApprovalSetting::getApprovalFlow() sudah memastikan SPV enabled
                // Ambil dari pre-loaded users (yang sudah di-query berdasarkan divisi asli)
                $isEnabled = true;
                $users = $usersByRole['spv_division'] ?? collect();
            } elseif ($roleKey === 'head_division') {
                // ApprovalSetting::getApprovalFlow() sudah memastikan HEAD enabled
                // Ambil dari pre-loaded users (yang sudah di-query berdasarkan divisi asli)
                $isEnabled = true;
                $users = $usersByRole['head_division'] ?? collect();
            } elseif ($roleKey === 'manager') {
                // ApprovalSetting::getApprovalFlow() sudah memastikan MANAGER enabled
                // Ambil dari pre-loaded users (yang sudah di-query berdasarkan divisi asli)
                $isEnabled = true;
                $users = $usersByRole['manager'] ?? collect();
            } elseif ($roleKey === 'hr') {
                // HRD selalu enabled (tidak ada flag di DivisiApprovalSetting)
                $isEnabled = true;
                // Ambil dari pre-loaded users
                $users = $usersByRole['hr'] ?? collect();
            } elseif ($setting->approver_type === 'user' && $setting->user_id) {
                // Jika approver_type adalah user, gunakan user_id dari ApprovalSetting
                $isEnabled = true;
                // Ambil dari pre-loaded users
                if (isset($usersById[$setting->user_id])) {
                    $users = collect([$usersById[$setting->user_id]]);
                } else {
                    $users = collect(); // Skip jika tidak ditemukan atau jabatan 7
                }
            } else {
                // Role tidak dikenali atau tidak enabled, skip
                continue;
            }

            // Hanya tambahkan ke chain jika enabled dan ada user
            if ($isEnabled && $users->isNotEmpty()) {
                // Gunakan approval_order sebagai key untuk menjaga urutan sesuai approval_hr_setting
                $chainKey = 'order_' . $approvalOrder;

                // Jika ada multiple users, buat entry untuk setiap user
                // Atau bisa juga simpan sebagai array users
                $chain[$chainKey] = [
                    'users' => $users, // Array/Collection of users
                    'user_id' => $users->first()->id, // User pertama untuk backward compatibility
                    'user' => $users->first(), // User pertama untuk backward compatibility
                    'level_name' => $levelName,
                    'approval_order' => $approvalOrder, // URUTAN dari approval_hr_setting
                    'role_key' => $roleKey,
                    'setting_id' => $setting->id,
                    'user_count' => $users->count() // Jumlah user untuk info
                ];
            }
        }

        // 5. Pastikan urutan sesuai approval_order dari approval_hr_setting
        //    Sort by approval_order untuk memastikan urutan benar
        uasort($chain, function ($a, $b) {
            return $a['approval_order'] <=> $b['approval_order'];
        });

        return $chain;
    }

    /**
     * Get approval chain untuk request dari Manager
     * Untuk request dari Manager (jabatan 3), approval chain adalah: General Manager -> HRD
     */
    protected function getManagerRequestApprovalChain(EmployeeRequest $request)
    {
        $chain = [];

        // 1. General Manager (order 1)
        // Cari General Manager dengan divisi 13
        $generalManagers = User::on('pgsql')
            ->where('divisi', 13)
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->get();

        if ($generalManagers->isNotEmpty()) {
            $chain['order_1'] = [
                'users' => $generalManagers,
                'user_id' => $generalManagers->first()->id,
                'user' => $generalManagers->first(),
                'level_name' => 'general_manager',
                'approval_order' => 1,
                'role_key' => 'general_manager',
                'setting_id' => null,
                'user_count' => $generalManagers->count()
            ];
        }

        // 2. HRD (order 2)
        // Cari semua HRD (divisi 7)
        $hrUsers = User::on('pgsql')
            ->where('divisi', 7)
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->get();

        if ($hrUsers->isNotEmpty()) {
            $chain['order_2'] = [
                'users' => $hrUsers,
                'user_id' => $hrUsers->first()->id,
                'user' => $hrUsers->first(),
                'level_name' => 'hr',
                'approval_order' => 2,
                'role_key' => 'hr',
                'setting_id' => null,
                'user_count' => $hrUsers->count()
            ];
        }

        return $chain;
    }

    /**
     * Get approval chain untuk request dari HEAD PRODUKSI
     * Untuk request dari HEAD PRODUKSI (jabatan 4, divisi 4), approval chain adalah: General Manager -> HRD
     * Karena Manager Produksi tidak ada, approval langsung ke General Manager
     */
    protected function getHeadProduksiRequestApprovalChain(EmployeeRequest $request)
    {
        $chain = [];

        // 1. General Manager (order 1)
        // Cari General Manager dengan divisi 13
        $generalManagers = User::on('pgsql')
            ->where('divisi', 13)
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->get();

        if ($generalManagers->isNotEmpty()) {
            $chain['order_1'] = [
                'users' => $generalManagers,
                'user_id' => $generalManagers->first()->id,
                'user' => $generalManagers->first(),
                'level_name' => 'general_manager',
                'approval_order' => 1,
                'role_key' => 'general_manager',
                'setting_id' => null,
                'user_count' => $generalManagers->count()
            ];
        }

        // 2. HRD (order 2)
        // Cari semua HRD (divisi 7)
        $hrUsers = User::on('pgsql')
            ->where('divisi', 7)
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->get();

        if ($hrUsers->isNotEmpty()) {
            $chain['order_2'] = [
                'users' => $hrUsers,
                'user_id' => $hrUsers->first()->id,
                'user' => $hrUsers->first(),
                'level_name' => 'hr',
                'approval_order' => 2,
                'role_key' => 'hr',
                'setting_id' => null,
                'user_count' => $hrUsers->count()
            ];
        }

        return $chain;
    }

    /**
     * Get approval chain untuk SHIFT_CHANGE
     * PENTING:
     * 1. Ambil URUTAN dari ApprovalSetting (tb_approval_hr_settings) berdasarkan approval_order
     * 2. TIDAK perlu cek DivisiApprovalSetting untuk skip level (semua level di ApprovalSetting digunakan)
     * 3. Ambil user approver berdasarkan role_key dari ApprovalSetting
     */
    protected function getShiftChangeApprovalChain(EmployeeRequest $request, $divisiId)
    {
        // 1. PENTING: Ambil flow dari ApprovalSetting berdasarkan request_type = 'shift_change'
        //    Sudah di-order by approval_order ASC
        $approvalFlow = ApprovalSetting::getApprovalFlow($request->request_type);
        // dd($approvalFlow);

        if ($approvalFlow->isEmpty()) {
            throw new \Exception('Setting approval untuk tipe perizinan tukar shift belum dikonfigurasi');
        }

        // 2. OPTIMIZATION: Batch query untuk semua user approvers sekaligus
        $userQueries = [];
        $userBasedIds = [];

        foreach ($approvalFlow as $setting) {
            $roleKey = $setting->role_key;

            if ($roleKey === 'spv_division') {
                $userQueries[] = ['divisi' => $divisiId, 'jabatan' => 5, 'role' => 'spv_division'];
            } elseif ($roleKey === 'head_division' || $roleKey === 'spv_division') {
                $userQueries[] = ['divisi' => $divisiId, 'jabatan' => 4, 'role' => 'head_division'];
            } elseif ($roleKey === 'manager') {
                $userQueries[] = ['divisi' => $divisiId, 'jabatan' => 3, 'role' => 'manager'];
            } elseif ($roleKey === 'hr') {
                $userQueries[] = ['divisi' => 7, 'jabatan' => null, 'role' => 'hr'];
            } elseif ($setting->approver_type === 'user' && $setting->user_id) {
                $userBasedIds[] = $setting->user_id;
            }
        }

        // dd($userQueries, $userBasedIds);

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
        $usersByRole = [];
        foreach ($allUsers as $user) {
            // Determine role based on jabatan and divisi
            $role = null;
            if ($user->jabatan == 5 && $user->divisi == $divisiId) {
                $role = 'spv_division';
            } elseif ($user->jabatan == 4 && $user->divisi == $divisiId) {
                $role = 'head_division';
            } elseif ($user->jabatan == 3 && $user->divisi == $divisiId) {
                $role = 'manager';
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
        //    SEMUA level di ApprovalSetting digunakan (tidak ada skip)
        $chain = [];

        foreach ($approvalFlow as $setting) {
            // Ambil data dari ApprovalSetting
            $roleKey = $setting->role_key;
            $approvalOrder = $setting->approval_order;
            $levelName = $setting->approval_level ?? $roleKey;
            $users = collect();

            // Ambil user approver dari pre-loaded users (batch query)
            if ($roleKey === 'spv_division') {
                // SPV: jabatan 5 di divisi yang sama
                $users = $usersByRole['spv_division'] ?? collect();
            } elseif ($roleKey === 'head_division') {
                // HEAD: jabatan 4 di divisi yang sama
                $users = $usersByRole['head_division'] ?? collect();
            } elseif ($roleKey === 'manager') {
                // MANAGER: jabatan 3 di divisi yang sama
                $users = $usersByRole['manager'] ?? collect();
            } elseif ($roleKey === 'hr') {
                // HR: divisi 7
                $users = $usersByRole['hr'] ?? collect();
            } elseif ($setting->approver_type === 'user' && $setting->user_id) {
                // User-based approver
                if (isset($usersById[$setting->user_id])) {
                    $users = collect([$usersById[$setting->user_id]]);
                } else {
                    $users = collect();
                }
            } else {
                // Role tidak dikenali, skip
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

        // Sort by approval_order untuk memastikan urutan benar
        uasort($chain, function ($a, $b) {
            return $a['approval_order'] <=> $b['approval_order'];
        });

        return $chain;
    }

    /**
     * Get next pending approver for a request
     * Berdasarkan field di EmployeeRequest (head_approved_at, manager_approved_at, dll)
     */
    public function getNextApprover(EmployeeRequest $request)
    {
        $chain = $this->getApprovalChain($request);

        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;
            $isApproved = false;
            $isRejected = false;

            // Cek status berdasarkan role_key dan field di EmployeeRequest
            if ($roleKey === 'spv_division') {
                $isApproved = !is_null($request->supervisor_approved_at);
                $isRejected = !is_null($request->supervisor_rejected_at);
            } elseif ($roleKey === 'head_division') {
                $isApproved = !is_null($request->head_approved_at);
                $isRejected = !is_null($request->head_rejected_at);
            } elseif ($roleKey === 'manager') {
                $isApproved = !is_null($request->manager_approved_at);
                $isRejected = !is_null($request->manager_rejected_at);
            } elseif ($roleKey === 'hr') {
                $isApproved = !is_null($request->hr_approved_at);
                $isRejected = !is_null($request->hr_rejected_at);
            }

            // Jika sudah di-reject, return null
            if ($isRejected) {
                return null;
            }

            // Jika belum di-approve, ini adalah next approver
            if (!$isApproved) {
                // Ambil user pertama dari users collection
                $users = $approverData['users'] ?? collect();
                if ($users->isNotEmpty()) {
                    $firstUser = $users->first();
                    return [
                        'level' => $level,
                        'user_id' => $firstUser->id,
                        'level_name' => $approverData['level_name'] ?? $level,
                        'users' => $users
                    ];
                }
            }
        }

        // All levels approved
        return null;
    }

    /**
     * Initialize approval records for a new request
     * Untuk ABSENCE: approval sudah di-track di field EmployeeRequest (head_approved_at, manager_approved_at, dll)
     * Tidak perlu create RequestApproval lagi
     */
    public function initializeApprovals(EmployeeRequest $request)
    {
        $chain = $this->getApprovalChain($request);

        // Debug log
        \Log::info('=== Initialize Approvals ===');
        \Log::info('Request ID: ' . $request->id);
        \Log::info('Request Type: ' . $request->request_type);
        \Log::info('Employee ID: ' . $request->employee_id);
        \Log::info('Employee Divisi: ' . ($request->employee ? $request->employee->divisi : 'N/A'));
        \Log::info('Approval Chain: ', $chain);

        // Untuk ABSENCE, approval sudah di-track di field EmployeeRequest
        // Tidak perlu create RequestApproval
        \Log::info('Total approval levels: ' . count($chain));
        \Log::info('=== End Initialize Approvals ===');

        return $chain;
    }

    /**
     * Process approval (approve or reject)
     * $level bisa berupa role_key (head_division, manager, hr) atau level identifier (order_X)
     * Update field di EmployeeRequest langsung
     */
    public function processApproval(EmployeeRequest $request, $level, $status, $approverId, $notes = null)
    {
        return DB::transaction(function () use ($request, $level, $status, $approverId, $notes) {
            // Tentukan role_key dari level
            // Jika level adalah 'order_X', ambil role_key dari chain
            $roleKey = null;
            $chain = $this->getApprovalChain($request);

            // Get approver user
            $approver = User::find($approverId);

            foreach ($chain as $chainLevel => $approverData) {
                $chainRoleKey = $approverData['role_key'] ?? null;

                // Jika level match dengan chainLevel atau role_key
                if ($chainLevel === $level || $chainRoleKey === $level) {
                    $roleKey = $chainRoleKey;
                    break;
                }

                // Untuk ABSENCE, cek apakah level adalah order_X
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE && isset($approverData['approval_order'])) {
                    $orderLevel = 'order_' . $approverData['approval_order'];
                    if ($orderLevel === $level) {
                        $roleKey = $chainRoleKey;
                        break;
                    }
                }
            }

            if (!$roleKey) {
                throw new \Exception('Level approval tidak ditemukan');
            }

            // Cek apakah sudah di-approve/reject
            $isAlreadyProcessed = false;
            if ($roleKey === 'spv_division') {
                $isAlreadyProcessed = !is_null($request->supervisor_approved_at) || !is_null($request->supervisor_rejected_at);
            } else if ($roleKey === 'head_division') {
                $isAlreadyProcessed = !is_null($request->head_approved_at) || !is_null($request->head_rejected_at);
            } elseif ($roleKey === 'manager') {
                $isAlreadyProcessed = !is_null($request->manager_approved_at) || !is_null($request->manager_rejected_at);
            } elseif ($roleKey === 'hr') {
                $isAlreadyProcessed = !is_null($request->hr_approved_at) || !is_null($request->hr_rejected_at);
            }

            if ($isAlreadyProcessed) {
                throw new \Exception('Request sudah diproses');
            }

            // Update field di EmployeeRequest berdasarkan role_key
            $updateData = [];
            $timestampField = null;
            $notesField = null;

            if ($roleKey === 'spv_division') {
                // SPV menggunakan field supervisor terpisah
                $timestampField = $status === 'approved' ? 'supervisor_approved_at' : 'supervisor_rejected_at';
                $notesField = 'supervisor_notes';
            } elseif ($roleKey === 'head_division') {
                // HEAD menggunakan field head terpisah
                $timestampField = $status === 'approved' ? 'head_approved_at' : 'head_rejected_at';
                $notesField = 'head_notes';
            } elseif ($roleKey === 'manager') {
                $timestampField = $status === 'approved' ? 'manager_approved_at' : 'manager_rejected_at';
                $notesField = 'manager_notes';
            } elseif ($roleKey === 'hr') {
                $timestampField = $status === 'approved' ? 'hr_approved_at' : 'hr_rejected_at';
                $notesField = 'hr_notes';
            }

            if ($timestampField) {
                $updateData[$timestampField] = now();
                if ($notesField && $notes) {
                    $updateData[$notesField] = $notes;
                }
                $request->update($updateData);
            }

            // If rejected, auto-reject all subsequent levels
            if ($status === 'rejected') {
                $this->rejectSubsequentLevels($request, $roleKey);
            }

            // Update request overall status
            $this->updateRequestStatus($request);

            // Send email notification
            $this->sendApprovalNotification($request, $roleKey, $status, $approver, $notes);

            return $request->fresh();
        });
    }

    /**
     * Reject all subsequent approval levels
     * Update field di EmployeeRequest untuk level-level setelah yang di-reject
     */
    protected function rejectSubsequentLevels(EmployeeRequest $request, $rejectedRoleKey)
    {
        $chain = $this->getApprovalChain($request);
        $foundRejected = false;

        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;

            if ($roleKey === $rejectedRoleKey) {
                $foundRejected = true;
                continue;
            }

            if (!$foundRejected) {
                continue;
            }

            // Update field di EmployeeRequest untuk level-level setelah yang di-reject
            // Note: Untuk level yang sudah di-approve, tidak perlu di-update
            $updateData = [];

            if ($roleKey === 'spv_division') {
                if (is_null($request->supervisor_approved_at) && is_null($request->supervisor_rejected_at)) {
                    $updateData['supervisor_rejected_at'] = now();
                    $updateData['supervisor_notes'] = 'Otomatis ditolak karena level sebelumnya ditolak';
                }
            } elseif ($roleKey === 'head_division') {
                if (is_null($request->head_approved_at) && is_null($request->head_rejected_at)) {
                    $updateData['head_rejected_at'] = now();
                    $updateData['head_notes'] = 'Otomatis ditolak karena level sebelumnya ditolak';
                }
            } elseif ($roleKey === 'manager') {
                if (is_null($request->manager_approved_at) && is_null($request->manager_rejected_at)) {
                    $updateData['manager_rejected_at'] = now();
                    $updateData['manager_notes'] = 'Otomatis ditolak karena level sebelumnya ditolak';
                }
            } elseif ($roleKey === 'hr') {
                if (is_null($request->hr_approved_at) && is_null($request->hr_rejected_at)) {
                    $updateData['hr_rejected_at'] = now();
                    $updateData['hr_notes'] = 'Otomatis ditolak karena level sebelumnya ditolak';
                }
            }

            if (!empty($updateData)) {
                $request->update($updateData);
            }
        }
    }

    /**
     * Update request overall status based on approvals
     * Berdasarkan field di EmployeeRequest (head_approved_at, manager_approved_at, dll)
     */
    public function updateRequestStatus(EmployeeRequest $request)
    {
        $chain = $this->getApprovalChain($request);
        $hasRejected = false;
        $allApproved = true;
        $currentOrder = 0; // Track current approval order

        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;
            $isApproved = false;
            $isRejected = false;

            // Cek status berdasarkan role_key
            if ($roleKey === 'spv_division') {
                $isApproved = !is_null($request->supervisor_approved_at);
                $isRejected = !is_null($request->supervisor_rejected_at);
            } else if ($roleKey === 'head_division') {
                $isApproved = !is_null($request->head_approved_at);
                $isRejected = !is_null($request->head_rejected_at);
            } elseif ($roleKey === 'manager') {
                $isApproved = !is_null($request->manager_approved_at);
                $isRejected = !is_null($request->manager_rejected_at);
            } elseif ($roleKey === 'general_manager') {
                $isApproved = !is_null($request->general_approved_at);
                $isRejected = !is_null($request->general_rejected_at);
            } elseif ($roleKey === 'hr') {
                $isApproved = !is_null($request->hr_approved_at);
                $isRejected = !is_null($request->hr_rejected_at);
            }

            if ($isRejected) {
                $hasRejected = true;
                break;
            }

            // Update current_order jika level ini sudah approve
            if ($isApproved) {
                $currentOrder = $approverData['approval_order'] ?? $level;
            }

            if (!$isApproved) {
                $allApproved = false;
            }
        }

        // Prepare update data
        $updateData = [];

        // Update current_approval_order
        $updateData['current_approval_order'] = $currentOrder;

        // Update overall status
        if ($hasRejected) {
            // Determine which rejection status based on timestamps
            // Urutan pengecekan: dari level tertinggi ke terendah (HR > General Manager > Manager > Head > Supervisor)
            if (!is_null($request->hr_rejected_at)) {
                $updateData['status'] = EmployeeRequest::STATUS_HR_REJECTED;
            } elseif (!is_null($request->general_rejected_at)) {
                // General Manager reject, untuk request dari Manager atau HEAD PRODUKSI, statusnya MANAGER_REJECTED
                $updateData['status'] = EmployeeRequest::STATUS_MANAGER_REJECTED;
            } elseif (!is_null($request->manager_rejected_at)) {
                $updateData['status'] = EmployeeRequest::STATUS_MANAGER_REJECTED;
            } elseif (!is_null($request->head_rejected_at)) {
                $updateData['status'] = EmployeeRequest::STATUS_SUPERVISOR_REJECTED;
            } else {
                $updateData['status'] = 'rejected';
            }
        } elseif ($allApproved) {
            $updateData['status'] = EmployeeRequest::STATUS_HR_APPROVED;
        } else {
            // Check specific statuses based on what's approved
            // Urutan pengecekan: dari level tertinggi ke terendah (HR > General Manager > Manager > Head > Supervisor)
            // Supervisor dan Head sama-sama menggunakan STATUS_SUPERVISOR_APPROVED
            if (!is_null($request->hr_approved_at)) {
                // HR sudah approve, status sudah di-handle di $allApproved
                $updateData['status'] = EmployeeRequest::STATUS_HR_APPROVED;
            } elseif (!is_null($request->general_approved_at)) {
                // General Manager sudah approve, tapi HR belum
                // Untuk request dari Manager atau HEAD PRODUKSI, statusnya MANAGER_APPROVED
                $updateData['status'] = EmployeeRequest::STATUS_MANAGER_APPROVED;
            } elseif (!is_null($request->manager_approved_at)) {
                // Manager sudah approve, tapi HR belum
                $updateData['status'] = EmployeeRequest::STATUS_MANAGER_APPROVED;
            } elseif (!is_null($request->head_approved_at)) {
                // Head sudah approve, tapi manager dan hr belum
                $updateData['status'] = EmployeeRequest::STATUS_SUPERVISOR_APPROVED;
            } elseif (!is_null($request->supervisor_approved_at)) {
                // Supervisor sudah approve, tapi level berikutnya belum
                $updateData['status'] = EmployeeRequest::STATUS_SUPERVISOR_APPROVED;
            } else {
                // Belum ada yang approve
                $updateData['status'] = EmployeeRequest::STATUS_PENDING;
            }
        }

        $request->update($updateData);
    }

    /**
     * Check if all approvals are completed
     * Berdasarkan field di EmployeeRequest
     */
    public function checkAllApproved(EmployeeRequest $request)
    {
        $chain = $this->getApprovalChain($request);

        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;
            $isApproved = false;

            // Cek status berdasarkan role_key
            if ($roleKey === 'spv_division') {
                $isApproved = !is_null($request->supervisor_approved_at);
            } else if ($roleKey === 'head_division') {
                $isApproved = !is_null($request->head_approved_at);
            } elseif ($roleKey === 'manager') {
                $isApproved = !is_null($request->manager_approved_at);
            } elseif ($roleKey === 'hr') {
                $isApproved = !is_null($request->hr_approved_at);
            }

            if (!$isApproved) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send email notification after approval/rejection
     */
    protected function sendApprovalNotification(EmployeeRequest $request, $roleKey, $status, $approver, $notes = null)
    {
        try {
            $notificationService = new EmployeeRequestNotificationService($this);

            if ($status === 'approved') {
                // Send approval notification
                $notificationService->notifyApproved($request, $approver, $roleKey);
            } elseif ($status === 'rejected') {
                // Send rejection notification
                $notificationService->notifyRejected($request, $approver, $roleKey, $notes);
            }

            Log::info('Approval notification sent successfully', [
                'request_id' => $request->id,
                'status' => $status,
                'role_key' => $roleKey,
            ]);
        } catch (\Exception $e) {
            // Log error but don't throw exception to avoid interrupting approval process
            Log::error('Failed to send approval notification', [
                'request_id' => $request->id,
                'status' => $status,
                'role_key' => $roleKey,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get pending requests for a user based on their role/division
     * Berdasarkan approval chain dan field di EmployeeRequest
     */
    public static function getPendingRequestsForUser($user, $requestType = null, $roleKey = null)
    {
        $userId = $user instanceof User ? $user->id : $user;
        $userObj = $user instanceof User ? $user : User::find($userId);

        if (!$userObj) {
            return collect();
        }

        // Ambil semua EmployeeRequest dengan status yang mungkin membutuhkan approval
        // - pending: untuk supervisor/head/manager
        // - supervisor_approved: untuk HR (jika HR setelah supervisor)
        // - manager_approved: untuk HR (jika HR setelah manager)
        $query = \App\Models\EmployeeRequest::whereIn('status', [
            \App\Models\EmployeeRequest::STATUS_PENDING,
            \App\Models\EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
            \App\Models\EmployeeRequest::STATUS_MANAGER_APPROVED,
            \App\Models\EmployeeRequest::STATUS_HEAD_APPROVED,
        ])->whereNotIn('status', [
            \App\Models\EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
            \App\Models\EmployeeRequest::STATUS_MANAGER_REJECTED,
            \App\Models\EmployeeRequest::STATUS_HR_REJECTED,
            \App\Models\EmployeeRequest::STATUS_HR_APPROVED,
            \App\Models\EmployeeRequest::STATUS_CANCELLED
        ]);

        // Filter by request type if specified
        if ($requestType) {
            $requestTypeMapping = [
                'absence' => 'absence',
                'shift_change' => 'shift_change',
                'overtime' => 'overtime',
                'vehicle_asset' => 'vehicle_asset',
            ];
            $typeToCheck = $requestTypeMapping[$requestType] ?? $requestType;
            $query->where('request_type', $typeToCheck);
        }

        $allPendingRequests = $query->get();
        $pendingForUser = collect();

        $approvalService = new self();

        foreach ($allPendingRequests as $request) {
            try {
                // Ambil approval chain untuk request ini
                $chain = $approvalService->getApprovalChain($request);

                // Cek apakah user ini ada di chain dan masih pending
                foreach ($chain as $level => $approverData) {
                    $users = $approverData['users'] ?? collect();
                    $roleKeyInChain = $approverData['role_key'] ?? null;

                    // Cek apakah user ini ada di list approvers untuk level ini
                    $userInLevel = $users->contains('id', $userId);

                    if ($userInLevel) {
                        // Cek apakah level ini masih pending (belum di-approve/reject)
                        $isPending = false;

                        if ($roleKeyInChain === 'spv_division') {
                            $isPending = is_null($request->supervisor_approved_at) && is_null($request->supervisor_rejected_at);
                        } else if ($roleKeyInChain === 'head_division') {
                            $isPending = is_null($request->head_approved_at) && is_null($request->head_rejected_at);
                        } elseif ($roleKeyInChain === 'manager') {
                            $isPending = is_null($request->manager_approved_at) && is_null($request->manager_rejected_at);
                        } elseif ($roleKeyInChain === 'general_manager') {
                            $isPending = is_null($request->general_approved_at) && is_null($request->general_rejected_at);
                        } elseif ($roleKeyInChain === 'hr') {
                            $isPending = is_null($request->hr_approved_at) && is_null($request->hr_rejected_at);
                        }

                        // Cek apakah level sebelumnya sudah di-approve (untuk memastikan sudah sampai ke level ini)
                        $previousLevelsApproved = true;
                        $foundCurrentLevel = false;

                        foreach ($chain as $prevLevel => $prevApproverData) {
                            if ($prevLevel === $level) {
                                $foundCurrentLevel = true;
                                break;
                            }

                            $prevRoleKey = $prevApproverData['role_key'] ?? null;
                            $prevApproved = false;

                            if ($prevRoleKey === 'spv_division') {
                                $prevApproved = !is_null($request->supervisor_approved_at);
                            } else if ($prevRoleKey === 'head_division') {
                                $prevApproved = !is_null($request->head_approved_at);
                            } elseif ($prevRoleKey === 'manager') {
                                $prevApproved = !is_null($request->manager_approved_at);
                            } elseif ($prevRoleKey === 'general_manager') {
                                $prevApproved = !is_null($request->general_approved_at);
                            } elseif ($prevRoleKey === 'hr') {
                                $prevApproved = !is_null($request->hr_approved_at);
                            }

                            if (!$prevApproved) {
                                $previousLevelsApproved = false;
                                break;
                            }
                        }

                        // Jika level ini pending dan level sebelumnya sudah di-approve, ini adalah pending request untuk user
                        if ($isPending && $previousLevelsApproved) {
                            $pendingForUser->push($request);
                            break; // Break dari loop level, lanjut ke request berikutnya
                        }
                    }
                }
            } catch (\Exception $e) {
                // Skip request jika error (misalnya divisi tidak dikonfigurasi)
                continue;
            }
        }

        return $pendingForUser->unique('id')->values();
    }
}
