<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VehicleAssetRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleAssetController extends Controller
{
    /**
     * Display a listing of vehicle/asset requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'vehicle'); // default to vehicle
        // dd($type);
        $query = VehicleAssetRequest::query();

        // Filter based on user role
        if ($user->canApprove()) {
            // dd('masuk sini');
            // PENTING: General Manager (divisi 13) melihat request yang dibuat oleh Manager (jabatan 3) atau HEAD PRODUKSI (jabatan 4, divisi 4)
            // Filter berdasarkan general_id, bukan divisi_id
            if ((int) $user->divisi === 13) {
                // Ambil semua ID Manager (jabatan 3) dan HEAD PRODUKSI (jabatan 4, divisi 4)
                $managerIds = \App\Models\User::where('jabatan', 3)->pluck('id');
                $headProduksiIds = \App\Models\User::where('jabatan', 4)->where('divisi', 4)->pluck('id');
                $allowedEmployeeIds = $managerIds->merge($headProduksiIds);

                // General Manager melihat request yang:
                // 1. Dibuat oleh Manager (jabatan 3) atau HEAD PRODUKSI (jabatan 4, divisi 4)
                // 2. Memiliki general_id = user.id (sudah di-assign) ATAU belum di-assign general_id (tampilkan ke semua GM)
                // 3. Belum di-approve/reject oleh General Manager ATAU sudah di-approve tapi belum HRGA
                $query->whereIn('employee_id', $allowedEmployeeIds)
                      ->where(function($q) use ($user) {
                          // Request yang sudah di-assign ke General Manager ini
                          $q->where('general_id', $user->id)
                            // ATAU request yang belum di-assign general_id (tampilkan ke semua General Manager)
                            ->orWhereNull('general_id');
                      })
                      ->where(function($q) {
                          // Tampilkan yang masih pending di General Manager
                          $q->where(function($pendingQ) {
                              $pendingQ->whereNull('general_approved_at')
                                       ->whereNull('general_rejected_at')
                                       ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);
                          })
                          // ATAU yang sudah di-approve General Manager tapi belum HRGA (untuk history/disapprove)
                          ->orWhere(function($approvedQ) {
                              $approvedQ->whereNotNull('general_approved_at')
                                        ->whereNull('hrga_at')
                                        ->where('status', VehicleAssetRequest::STATUS_MANAGER_APPROVED);
                          });
                      });
            } else {
                // dd('sini');
                // Manager/Head/SPV can see their division's requests
                $query->where('divisi_id', $user->divisi);

                // PENTING: Untuk HEAD PRODUKSI (jabatan 4, divisi 4), filter out request sendiri
                // Karena request dari HEAD PRODUKSI langsung ke General Manager, bukan ke Manager biasa
                if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
                    // dd('masuk sini 3');
                    // HEAD PRODUKSI tidak bisa approve request sendiri (karena langsung ke General Manager)
                    // Tapi bisa approve request dari karyawan lain di divisinya (bukan HEAD PRODUKSI)
                    $headProduksiIds = \App\Models\User::where('jabatan', 4)->where('divisi', 4)->pluck('id');
                    $query->whereNotIn('employee_id', $headProduksiIds);

                    // Cek apakah HEAD ada di approval flow untuk vehicle_asset
                    $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
                    $headInVehicleFlow = false;
                    $headApprovalOrder = null;

                    foreach ($vehicleAssetFlow as $setting) {
                        if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user)) {
                            $headInVehicleFlow = true;
                            $headApprovalOrder = $setting->approval_order;
                            break;
                        }
                    }

                    if ($headInVehicleFlow && $headApprovalOrder == 1) {
                        // HEAD adalah approver pertama, hanya tampilkan yang status PENDING_MANAGER dan manager_at NULL
                        $query->whereNull('manager_at')
                              ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);
                    } elseif ($headInVehicleFlow && $headApprovalOrder > 1) {
                        // HEAD bukan approver pertama, perlu cek approval sebelumnya sudah selesai
                        // Untuk VehicleAssetRequest, jika HEAD di order > 1, berarti ada SPV sebelumnya
                        $query->whereNotNull('manager_at')
                              ->whereNull('general_approved_at')
                              ->whereNull('general_rejected_at')
                              ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);
                    } else {
                        // HEAD tidak ada di approval flow, tidak ada request untuk HEAD
                        $query->whereRaw('1 = 0'); // Return empty result
                    }
                }
                // PENTING: Untuk SPV (jabatan 5), filter berdasarkan approval flow
                // Hanya tampilkan request yang benar-benar bisa di-approve oleh SPV
                elseif ((int) $user->jabatan === 5) {
                    // Cek apakah SPV ada di approval flow untuk vehicle_asset
                    $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
                    $spvInVehicleFlow = false;
                    $spvApprovalOrder = null;

                    foreach ($vehicleAssetFlow as $setting) {
                        if ($setting->role_key === 'spv_division' ||
                            ($setting->role_key === 'head_division' && (int) $user->jabatan === 5 && $setting->isUserAllowedToApprove($user))) {
                            $spvInVehicleFlow = true;
                            $spvApprovalOrder = $setting->approval_order;
                            break;
                        }
                    }

                    if ($spvInVehicleFlow && $spvApprovalOrder == 1) {
                        // SPV adalah approver pertama, hanya tampilkan yang status PENDING_MANAGER dan manager_at NULL
                        $query->whereNull('manager_at')
                              ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);
                    } elseif ($spvInVehicleFlow && $spvApprovalOrder > 1) {
                        // SPV bukan approver pertama, tidak ada request untuk SPV (karena approval sebelumnya belum selesai)
                        $query->whereRaw('1 = 0'); // Return empty result
                    } else {
                        // SPV tidak ada di approval flow, tidak ada request untuk SPV
                        $query->whereRaw('1 = 0'); // Return empty result
                    }
                }
            }
        } else {
            // dd('masuk sini 2');
            // Regular staff can only see their own requests
            $query->where('employee_id', $user->id);
        }

        // Filter by request type
        $query->where('request_type', $type);

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        // dd($requests);

        return view('hr.vehicle-asset.index', compact('requests', 'type'));
    }

    /**
     * Show the form for creating a new vehicle/asset request
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'vehicle');
        return view('hr.vehicle-asset.create', compact('type'));
    }

    /**
     * Display the specified vehicle/asset request
     */
    public function show($id)
    {
        // dd($id);
        $user = Auth::user();
        $request = VehicleAssetRequest::findOrFail($id);

        // dd($request);
        // Authorization check - allow viewing if:
        // 1. HR always can view
        // 2. Employee who created the request
        // 3. User who has approved/rejected the request (manager, general manager, or HRGA)
        // 4. User from same division who can approve (for pending requests)

        $canView = false;

        // HR selalu bisa melihat semua request
        if ($user->isHR()) {
            $canView = true;
        }

        // Employee yang membuat request bisa melihat
        if ($request->employee_id == $user->id) {
            $canView = true;
        }

        // User yang sudah melakukan approval bisa melihat (meskipun sudah di-approve HR)
        if ($request->manager_id == $user->id ||
            $request->general_id == $user->id ||
            $request->hrga_id == $user->id) {
            $canView = true;
        }

        // Jika belum bisa view, cek apakah user bisa approve (untuk pending requests)
        if (!$canView && $user->canApprove()) {
            // PENTING: General Manager (divisi 13) bisa melihat request yang dibuat oleh Manager (jabatan 3)
            // yang memiliki general_id = user.id, bukan berdasarkan divisi_id
            if ((int) $user->divisi === 13) {
                // General Manager bisa melihat jika general_id = user.id atau manager_id = user.id (backward compatibility)
                if ($request->general_id == $user->id || $request->manager_id == $user->id) {
                    $canView = true;
                }
            } else {
                // Manager/Head/SPV can see their division's requests
                if ($request->divisi_id == $user->divisi) {
                    $canView = true;
                }
            }
        }

        if (!$canView) {
            abort(403, 'Anda tidak memiliki akses untuk request ini.');
        }

        // Check if user can approve this request
        // PENTING: Jika request dibuat oleh Manager (jabatan 3) atau HEAD PRODUKSI (jabatan 4, divisi 4),
        // maka hanya General Manager (divisi 13) yang bisa approve
        // Jika request dibuat oleh non-Manager/HEAD PRODUKSI, baru Manager biasa yang bisa approve (jika ada di approval flow)
        $canApprove = false;

        // Cek apakah employee yang membuat request adalah Manager (jabatan 3) atau HEAD PRODUKSI (jabatan 4, divisi 4)
        $employee = \App\Models\User::find($request->employee_id);
        $isRequestFromManager = $employee && (int) $employee->jabatan === 3;
        $isRequestFromHeadProduksi = $employee && (int) $employee->jabatan === 4 && (int) $employee->divisi === 4;

        // PENTING: HEAD PRODUKSI tidak bisa approve request sendiri (karena langsung ke General Manager)
        if ((int) $user->jabatan === 4 && (int) $user->divisi === 4 && $isRequestFromHeadProduksi) {
            // User adalah HEAD PRODUKSI dan request juga dari HEAD PRODUKSI
            // Langsung set false, tidak perlu cek lebih lanjut
            $canApprove = false;
        } elseif ($isRequestFromManager || $isRequestFromHeadProduksi) {
            // Request dibuat oleh Manager atau HEAD PRODUKSI: hanya General Manager (divisi 13) yang bisa approve
            if ((int) $user->divisi === 13) {
                // Cek apakah request ini memang untuk General Manager (general_id = user.id atau manager_id = user.id untuk backward compatibility)
                // Dan status masih pending, belum di-approve/reject
                $isAssignedToGeneralManager = ($request->general_id == $user->id) || ($request->manager_id == $user->id);
                $isStillPending = is_null($request->general_approved_at) && is_null($request->general_rejected_at);
                $isPendingStatus = $request->status === VehicleAssetRequest::STATUS_PENDING_MANAGER;

                $canApprove = $isAssignedToGeneralManager && $isStillPending && $isPendingStatus;
            }
        } else {
            // Request dibuat oleh non-Manager/HEAD PRODUKSI: Manager/HEAD biasa bisa approve jika ada di approval flow
            // Cek apakah MANAGER atau HEAD ada di approval flow untuk vehicle_asset
            $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
            $managerInVehicleFlow = false;
            $headInVehicleFlow = false;
            $managerApprovalOrder = null;
            $headApprovalOrder = null;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'manager' ||
                    ($setting->role_key === 'head_division' && (int) $user->jabatan === 3 && $setting->isUserAllowedToApprove($user))) {
                    $managerInVehicleFlow = true;
                    $managerApprovalOrder = $setting->approval_order;
                }
                if ($setting->role_key === 'head_division' && (int) $user->jabatan === 4 && $setting->isUserAllowedToApprove($user)) {
                    $headInVehicleFlow = true;
                    $headApprovalOrder = $setting->approval_order;
                }
            }

            // Untuk Manager (jabatan 3)
            if ($managerInVehicleFlow && (int) $user->jabatan === 3 && $request->divisi_id == $user->divisi) {
                // Manager ada di flow dan user adalah Manager
                if ($managerApprovalOrder == 1) {
                    // Manager adalah approver pertama
                    $canApprove = $request->isPendingManager() && is_null($request->manager_at);
                } else {
                    // Manager bukan approver pertama, perlu cek approval sebelumnya sudah selesai
                    // Untuk VehicleAssetRequest, jika Manager di order > 1, berarti ada SPV/HEAD sebelumnya
                    // Tapi karena VehicleAssetRequest hanya punya manager_at, mungkin Manager tidak pernah di order > 1
                    $canApprove = false;
                }
            }

            // Untuk HEAD (jabatan 4) - KECUALI HEAD PRODUKSI (divisi 4) yang approve request sendiri
            // HEAD PRODUKSI bisa approve request dari karyawan lain di divisinya (bukan request sendiri)
            if ($headInVehicleFlow && (int) $user->jabatan === 4 && $request->divisi_id == $user->divisi) {
                // Pastikan request bukan dari HEAD PRODUKSI sendiri
                $isRequestFromHeadProduksi = $employee && (int) $employee->jabatan === 4 && (int) $employee->divisi === 4;

                if (!$isRequestFromHeadProduksi) {
                    // HEAD ada di flow dan request bukan dari HEAD PRODUKSI sendiri
                    if ($headApprovalOrder == 1) {
                        // HEAD adalah approver pertama
                        $canApprove = $request->isPendingManager() && is_null($request->manager_at);
                    } else {
                        // HEAD bukan approver pertama, perlu cek approval sebelumnya sudah selesai
                        $canApprove = $request->isPendingManager() &&
                                     !is_null($request->manager_at) &&
                                     is_null($request->general_approved_at) &&
                                     is_null($request->general_rejected_at);
                    }
                }
            }
        }

        // Cek apakah request bisa diedit (hanya oleh pembuat request dan belum ada approval sama sekali)
        $canEdit = false;
        if ($request->employee_id == $user->id) {
            // Bisa diedit jika belum ada approval sama sekali
            $canEdit = is_null($request->manager_at) &&
                      is_null($request->general_approved_at) &&
                      is_null($request->general_rejected_at) &&
                      is_null($request->hrga_at) &&
                      $request->status === VehicleAssetRequest::STATUS_PENDING_MANAGER;
        }

        return view('hr.vehicle-asset.show', compact('request', 'canApprove', 'canEdit'));
    }

    /**
     * Show the form for editing the specified vehicle/asset request
     */
    public function edit($id)
    {
        $user = Auth::user();
        $request = VehicleAssetRequest::findOrFail($id);

        // Authorization: hanya pembuat request yang bisa edit
        if ($request->employee_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit request ini.');
        }

        // Cek apakah request masih bisa diedit (belum ada approval sama sekali)
        if (!(is_null($request->manager_at) &&
              is_null($request->general_approved_at) &&
              is_null($request->general_rejected_at) &&
              is_null($request->hrga_at) &&
              $request->status === VehicleAssetRequest::STATUS_PENDING_MANAGER)) {
            return redirect()->route('hr.vehicle-asset.show', $id)
                ->with('error', 'Request tidak dapat diedit karena sudah ada approval.');
        }

        $type = $request->request_type;
        return view('hr.vehicle-asset.edit', compact('request', 'type'));
    }

    /**
     * Update the specified vehicle/asset request
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'request_type' => 'required|in:vehicle,asset',
            'request_date' => 'required|date',
            'vehicle_type' => 'required_if:request_type,vehicle|string|max:255',
            'asset_category' => 'required_if:request_type,asset|string|max:255',
            'purpose_type' => 'required|string|max:255',
            'purpose' => 'required|string',
            'destination' => 'required|string|max:255',
            'license_plate' => 'nullable|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            $user = Auth::user();
            $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

            // Authorization: hanya pembuat request yang bisa edit
            if ($vehicleAssetRequest->employee_id != $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit request ini.');
            }

            // Cek apakah request masih bisa diedit (belum ada approval sama sekali)
            if (!(is_null($vehicleAssetRequest->manager_at) &&
                  is_null($vehicleAssetRequest->general_approved_at) &&
                  is_null($vehicleAssetRequest->general_rejected_at) &&
                  is_null($vehicleAssetRequest->hrga_at) &&
                  $vehicleAssetRequest->status === VehicleAssetRequest::STATUS_PENDING_MANAGER)) {
                return redirect()->route('hr.vehicle-asset.show', $id)
                    ->with('error', 'Request tidak dapat diedit karena sudah ada approval.');
            }

            // Update request
            $vehicleAssetRequest->update([
                'request_date' => $request->request_date,
                'request_type' => $request->request_type,
                'vehicle_type' => $request->vehicle_type,
                'asset_category' => $request->asset_category,
                'purpose_type' => $request->purpose_type,
                'purpose' => $request->purpose,
                'destination' => $request->destination,
                'license_plate' => $request->license_plate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
            ]);

            // Jika request dibuat oleh Manager atau HEAD PRODUKSI, pastikan general_id masih di-set
            if ((int) $user->jabatan === 3 || ((int) $user->jabatan === 4 && (int) $user->divisi === 4)) {
                if (is_null($vehicleAssetRequest->general_id)) {
                    $this->handleManagerVehicleAssetApproval($vehicleAssetRequest, $user);
                }
            }

            DB::connection('pgsql2')->commit();

            return redirect()->route('hr.vehicle-asset.show', $id)
                ->with('success', 'Permintaan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vehicle/asset request
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

            // Authorization: hanya pembuat request yang bisa hapus
            if ($vehicleAssetRequest->employee_id != $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus request ini.');
            }

            // Cek apakah request masih bisa dihapus (belum ada approval sama sekali)
            if (!(is_null($vehicleAssetRequest->manager_at) &&
                  is_null($vehicleAssetRequest->general_approved_at) &&
                  is_null($vehicleAssetRequest->general_rejected_at) &&
                  is_null($vehicleAssetRequest->hrga_at) &&
                  $vehicleAssetRequest->status === VehicleAssetRequest::STATUS_PENDING_MANAGER)) {
                return redirect()->route('hr.vehicle-asset.index', ['type' => $vehicleAssetRequest->request_type])
                    ->with('error', 'Request tidak dapat dihapus karena sudah ada approval.');
            }

            $requestType = $vehicleAssetRequest->request_type;
            $vehicleAssetRequest->delete();

            return redirect()->route('hr.vehicle-asset.index', ['type' => $requestType])
                ->with('success', 'Permintaan berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created vehicle/asset request
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_type' => 'required|in:vehicle,asset',
            'request_date' => 'required|date',
            'vehicle_type' => 'required_if:request_type,vehicle|string|max:255',
            'asset_category' => 'required_if:request_type,asset|string|max:255',
            'purpose_type' => 'required|string|max:255',
            'purpose' => 'required|string',
            'destination' => 'required|string|max:255',
            'license_plate' => 'nullable|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'request_number' => 'nullable|string|max:255',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            $user = Auth::user();

            // Request number otomatis
            $requestNumber = ($request->request_type === 'vehicle' ? 'VH-' : 'AS-') . str_pad(VehicleAssetRequest::count() + 1, 4, '0', STR_PAD_LEFT);
            $request->request_number = $requestNumber;

            $vehicleAssetRequest = VehicleAssetRequest::create([
                'request_date' => $request->request_date,
                'request_type' => $request->request_type,
                'employee_id' => $user->id,
                'employee_name' => $user->name,
                'department' => $user->department ?? 'N/A',
                'divisi_id' => $user->divisi,
                'vehicle_type' => $request->vehicle_type,
                'asset_category' => $request->asset_category,
                'purpose_type' => $request->purpose_type,
                'purpose' => $request->purpose,
                'destination' => $request->destination,
                'license_plate' => $request->license_plate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => VehicleAssetRequest::STATUS_PENDING_MANAGER,
                'request_number' => $requestNumber,
            ]);

            // KHUSUS UNTUK MANAGER: Set approver ke General Manager (divisi 13) untuk semua perizinan
            if ((int) $user->jabatan === 3) {
                $this->handleManagerVehicleAssetApproval($vehicleAssetRequest, $user);
            }

            // KHUSUS UNTUK HEAD PRODUKSI (jabatan 4, divisi 4): Set approver ke General Manager (divisi 13)
            // Karena Manager Produksi tidak ada, approval langsung ke General Manager
            if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
                // dd('masuk sini');
                $this->handleManagerVehicleAssetApproval($vehicleAssetRequest, $user);
            }

            DB::connection('pgsql2')->commit();

            return redirect()->route('hr.vehicle-asset.index', ['type' => $request->request_type])
                ->with('success', 'Permintaan berhasil diajukan dan menunggu persetujuan Manager.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display manager pending requests
     * Mengikuti ApprovalSetting: hanya tampil jika MANAGER ada di approval flow
     * Hanya bisa diakses oleh MANAGER (jabatan = 3)
     */
    public function managerPending(Request $request)
    {
        $user = Auth::user();

        // Authorization: Hanya MANAGER yang bisa akses (jabatan = 3)
        if ($user->jabatan != 3) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya MANAGER yang dapat mengakses.');
        }

        // Get employee IDs in same division
        $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

        // Gunakan ApprovalService untuk mendapatkan request yang benar-benar pending di level MANAGER
        // ApprovalService akan otomatis cek approval flow dan hanya return request yang sesuai
        $allPendingRequests = \App\Services\ApprovalService::getPendingRequestsForUser($user, null);

        // Filter Form Karyawan (absence, shift_change) yang benar-benar pending di level MANAGER
        $formRequests = $allPendingRequests->filter(function($request) use ($user) {
            if (!($request instanceof \App\Models\EmployeeRequest) ||
                !in_array($request->request_type, ['shift_change', 'absence'])) {
                return false;
            }

            // Pastikan manager_approved_at masih NULL
            if (!is_null($request->manager_approved_at) || !is_null($request->manager_rejected_at)) {
                return false;
            }

            // Pastikan request dari divisi yang sama
            $requester = \App\Models\User::find($request->employee_id);
            if (!$requester || $requester->divisi != $user->divisi) {
                return false;
            }

            // Cek apakah request ini benar-benar pending di level MANAGER berdasarkan approval flow
            $requestType = $request->request_type;
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($requestType);

            // Cek apakah MANAGER ada di approval flow
            $managerSetting = null;
            $managerApprovalOrder = null;

            foreach ($approvalFlow as $setting) {
                if ($setting->role_key === 'manager' ||
                    ($setting->role_key === 'head_division' && $user->jabatan == 3 && $setting->isUserAllowedToApprove($user))) {
                    $managerSetting = $setting;
                    $managerApprovalOrder = $setting->approval_order;
                    break;
                }
            }

            // Jika MANAGER tidak ada di approval flow, skip
            if (!$managerSetting || !$managerApprovalOrder) {
                return false;
            }

            // Cek apakah semua approval sebelum MANAGER sudah selesai
            if ($managerApprovalOrder == 1) {
                // MANAGER di urutan 1: belum ada approval sama sekali
                return is_null($request->head_approved_at) &&
                       is_null($request->head_rejected_at) &&
                       is_null($request->supervisor_approved_at) &&
                       is_null($request->supervisor_rejected_at);
            } else {
                // MANAGER di urutan > 1: harus semua approval sebelumnya sudah selesai
                for ($i = 1; $i < $managerApprovalOrder; $i++) {
                    $prevApprover = $approvalFlow->where('approval_order', $i)->first();
                    if ($prevApprover) {
                        if ($prevApprover->role_key === 'spv_division') {
                            // Harus sudah diapprove SPV
                            if (is_null($request->supervisor_approved_at) || !is_null($request->supervisor_rejected_at)) {
                                return false;
                            }
                        } elseif ($prevApprover->role_key === 'head_division') {
                            // Harus sudah diapprove HEAD
                            if (is_null($request->head_approved_at) || !is_null($request->head_rejected_at)) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            }
        });

        // Overtime: skip untuk sekarang, sistemnya nanti sendiri
        $overtimeRequests = collect();

        // Query Vehicle/Asset berdasarkan manager_at yang NULL
        // Cek apakah MANAGER ada di approval flow untuk vehicle_asset
        $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
        $managerInVehicleFlow = false;

        foreach ($vehicleAssetFlow as $setting) {
            if ($setting->role_key === 'manager' ||
                ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user))) {
                $managerInVehicleFlow = true;
                break;
            }
        }

        $vehicleRequests = collect();
        $assetRequests = collect();

        if ($managerInVehicleFlow) {
            $vehicleQuery = VehicleAssetRequest::where('request_type', 'vehicle')
                ->where('divisi_id', $user->divisi)
                ->whereNull('manager_at')
                ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);

            $vehicleRequests = $vehicleQuery->get();

            $assetQuery = VehicleAssetRequest::where('request_type', 'asset')
                ->where('divisi_id', $user->divisi)
                ->whereNull('manager_at')
                ->where('status', VehicleAssetRequest::STATUS_PENDING_MANAGER);

            $assetRequests = $assetQuery->get();
        }

        return view('hr.vehicle-asset.manager-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests'));
    }

    /**
     * Approve a request by manager
     */
    public function managerApprove(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canApprove()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

        // PENTING: General Manager (divisi 13) bisa approve request yang dibuat oleh Manager (jabatan 3)
        // yang memiliki general_id = user.id, bukan berdasarkan divisi_id
        if ((int) $user->divisi === 13) {
            // General Manager: cek apakah request ini untuk General Manager
            if ($vehicleAssetRequest->general_id != $user->id && $vehicleAssetRequest->manager_id != $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk request ini.');
            }

            // Cek apakah sudah di-approve/reject
            if (!is_null($vehicleAssetRequest->general_approved_at) || !is_null($vehicleAssetRequest->general_rejected_at)) {
                return back()->with('error', 'Request ini sudah diproses.');
            }
        } else {
            // Manager biasa: cek divisi
            if ($vehicleAssetRequest->divisi_id != $user->divisi) {
                abort(403, 'Anda tidak memiliki akses untuk request ini.');
            }

            if (!$vehicleAssetRequest->isPendingManager()) {
                return back()->with('error', 'Request ini sudah diproses.');
            }
        }

        $request->validate([
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            // PENTING: Untuk General Manager, update general_approved_at, bukan manager_at
            if ((int) $user->divisi === 13) {
                $vehicleAssetRequest->update([
                    'status' => VehicleAssetRequest::STATUS_MANAGER_APPROVED,
                    'general_id' => $user->id,
                    'general_approved_at' => now(),
                    'general_notes' => $request->manager_notes,
                    'manager_id' => $user->id, // Set juga manager_id untuk backward compatibility
                    'manager_at' => now(),
                ]);
            } else {
                $vehicleAssetRequest->update([
                    'status' => VehicleAssetRequest::STATUS_MANAGER_APPROVED,
                    'manager_id' => $user->id,
                    'manager_notes' => $request->manager_notes,
                    'manager_at' => now(),
                ]);
            }

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Request berhasil disetujui dan diteruskan ke HRGA.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject a request by manager
     */
    public function managerReject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canApprove()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

        // PENTING: General Manager (divisi 13) bisa reject request yang dibuat oleh Manager (jabatan 3)
        // yang memiliki general_id = user.id, bukan berdasarkan divisi_id
        if ((int) $user->divisi === 13) {
            // General Manager: cek apakah request ini untuk General Manager
            if ($vehicleAssetRequest->general_id != $user->id && $vehicleAssetRequest->manager_id != $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk request ini.');
            }

            // Cek apakah sudah di-approve/reject
            if (!is_null($vehicleAssetRequest->general_approved_at) || !is_null($vehicleAssetRequest->general_rejected_at)) {
                return back()->with('error', 'Request ini sudah diproses.');
            }
        } else {
            // Manager biasa: cek divisi
            if ($vehicleAssetRequest->divisi_id != $user->divisi) {
                abort(403, 'Anda tidak memiliki akses untuk request ini.');
            }

            if (!$vehicleAssetRequest->isPendingManager()) {
                return back()->with('error', 'Request ini sudah diproses.');
            }
        }

        $request->validate([
            'manager_notes' => 'required|string|max:1000',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            // PENTING: Untuk General Manager, update general_rejected_at, bukan manager_at
            if ((int) $user->divisi === 13) {
                $vehicleAssetRequest->update([
                    'status' => VehicleAssetRequest::STATUS_MANAGER_REJECTED,
                    'general_id' => $user->id,
                    'general_rejected_at' => now(),
                    'general_notes' => $request->manager_notes,
                    'manager_id' => $user->id, // Set juga manager_id untuk backward compatibility
                    'manager_at' => now(),
                ]);
            } else {
                $vehicleAssetRequest->update([
                    'status' => VehicleAssetRequest::STATUS_MANAGER_REJECTED,
                    'manager_id' => $user->id,
                    'manager_notes' => $request->manager_notes,
                    'manager_at' => now(),
                ]);
            }

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Request berhasil ditolak.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve requests by manager
     */
    public function managerBulkApprove(Request $request)
    {
        $user = Auth::user();
        // dd($user);
        if (!$user->canApprove()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $request->validate([
            'request_ids' => 'required|array',
            // 'request_ids.*' => 'integer|exists:tb_vehicle_asset_requests,id',
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        // dd($request->all());

        try {
            DB::connection('pgsql2')->beginTransaction();

            $requests = VehicleAssetRequest::whereIn('id', $request->request_ids)
                ->where('divisi_id', $user->divisi)
                ->pendingManager()
                ->get();

            // dd($requests);

            foreach ($requests as $vehicleAssetRequest) {
                $vehicleAssetRequest->update([
                    'status' => VehicleAssetRequest::STATUS_MANAGER_APPROVED,
                    'manager_id' => $user->id,
                    'manager_notes' => $request->manager_notes,
                    'manager_at' => now(),
                ]);
            }

            DB::connection('pgsql2')->commit();

            return back()->with('success', "Berhasil menyetujui {$requests->count()} request.");

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display HRGA pending requests
     */
    public function hrgaPending(Request $request)
    {
        $user = Auth::user();

        // Get pending requests using ApprovalService
        $pendingRequests = ApprovalService::getPendingRequestsForUser($user, 'vehicle_asset')
            ->filter(function($req) {
                return $req instanceof VehicleAssetRequest
                    && $req->status === VehicleAssetRequest::STATUS_MANAGER_APPROVED;
            });

        $type = $request->get('type', 'vehicle');

        // Filter by request type
        $pendingRequests = $pendingRequests->filter(function($req) use ($type) {
            return $req->request_type === $type;
        });

        // Convert to paginated collection
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $items = $pendingRequests->forPage($currentPage, $perPage);
        $requests = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $pendingRequests->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('hr.vehicle-asset.hrga-pending', compact('requests', 'type'));
    }

    /**
     * Approve a request by HRGA
     */
    public function hrgaApprove(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

        if (!$vehicleAssetRequest->isManagerApproved()) {
            return back()->with('error', 'Request ini belum disetujui Manager.');
        }

        $request->validate([
            'hrga_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            $vehicleAssetRequest->update([
                'status' => VehicleAssetRequest::STATUS_HRGA_APPROVED,
                'hrga_id' => $user->id,
                'hrga_notes' => $request->hrga_notes,
                'hrga_at' => now(),
            ]);

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Request berhasil disetujui HRGA.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject a request by HRGA
     */
    public function hrgaReject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $vehicleAssetRequest = VehicleAssetRequest::findOrFail($id);

        if (!$vehicleAssetRequest->isManagerApproved()) {
            return back()->with('error', 'Request ini belum disetujui Manager.');
        }

        $request->validate([
            'hrga_notes' => 'required|string|max:1000',
        ]);

        try {
            DB::connection('pgsql2')->beginTransaction();

            $vehicleAssetRequest->update([
                'status' => VehicleAssetRequest::STATUS_HRGA_REJECTED,
                'hrga_id' => $user->id,
                'hrga_notes' => $request->hrga_notes,
                'hrga_at' => now(),
            ]);

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Request berhasil ditolak HRGA.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display HRGA approved requests
     */
    public function hrgaApproved(Request $request)
    {
        $user = Auth::user();

        if (!$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        $type = $request->get('type', 'vehicle');

        $requests = VehicleAssetRequest::where('request_type', $type)
            ->approvedHrga()
            ->orderBy('hrga_at', 'desc')
            ->paginate(20);

        return view('hr.vehicle-asset.hrga-approved', compact('requests', 'type'));
    }

    /**
     * Handle approval untuk vehicle/asset request yang dibuat oleh MANAGER (jabatan 3)
     * Semua perizinan dari MANAGER harus diapprove oleh General Manager (divisi 13) dulu, baru ke HRD
     */
    private function handleManagerVehicleAssetApproval(VehicleAssetRequest $request, User $user)
    {
        // Cari General Manager dengan divisi 13
        // Prioritas: manager (3) dulu, baru head (4)
        $generalManager = \App\Models\User::on('pgsql')
            ->where('divisi', 13)
            ->whereIn('jabatan', [3, 4])
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->orderBy('jabatan', 'asc') // Prioritas manager (3) dulu
            ->first();

        if ($generalManager) {
            // Set general_id untuk General Manager approval (jika kolom sudah ada)
            // Jika kolom general_id belum ada, gunakan manager_id sebagai fallback
            try {
                $request->update([
                    'general_id' => $generalManager->id
                ]);
            } catch (\Exception $e) {
                // Fallback: jika kolom general_id belum ada, gunakan manager_id
                $request->update([
                    'manager_id' => $generalManager->id
                ]);
            }

            \Log::info('General Manager approver di-set untuk vehicle/asset request dari MANAGER atau HEAD PRODUKSI', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id,
                'employee_jabatan' => $user->jabatan,
                'employee_divisi' => $user->divisi,
                'general_manager_id' => $generalManager->id,
                'general_manager_jabatan' => $generalManager->jabatan
            ]);
        } else {
            \Log::warning('General Manager tidak ditemukan: User dengan divisi 13 tidak ditemukan', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id
            ]);
        }
    }
}
