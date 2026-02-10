<?php

namespace App\Http\Controllers;

use App\Models\OvertimeEntry;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OvertimeController extends Controller
{
    // Staff views
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = OvertimeEntry::query()->orderByDesc('request_date');

        // Filter berdasarkan role user
        if ($user->canApprove()) {
            // SPV/Head bisa lihat semua data divisi mereka
            $query->forDivisi($user->divisi);
        } else {
            // Staff biasa hanya bisa lihat data mereka sendiri
            $query->where('employee_id', $user->id)->orWhere('employee_name', $user->name);
        }

        $entries = $query->paginate(15);

        // Jika user adalah SPV (jabatan 5), tampilkan daftar menunggu persetujuan SPV di index
        $spvPending = null;
        if ((int)($user->jabatan ?? 0) === 5) {
            $spvPending = OvertimeEntry::forDivisi($user->divisi)
                ->pendingSpv()
                ->orderBy('request_date')
                ->limit(50)
                ->get();
        }

        return view('hr.overtime.index', compact('entries', 'spvPending'));
    }

    public function create()
    {
        $user = Auth::user();

        // Form overtime hanya bisa diakses oleh SPV (jabatan 5) atau yang bisa approve
        // SPV (jabatan 5), MANAGER (jabatan 3), atau HEAD (jabatan 4)
        if (!$user->canApprove() && (int)($user->jabatan ?? 0) !== 5) {
            abort(403, 'Hanya SPV/Manager/Head yang dapat mengisi form overtime.');
        }

        // Get employees from supervisor's division
        // SPV hanya bisa memilih karyawan dari divisi mereka
        $employees = User::where('divisi', $user->divisi)
            ->where('id', '!=', $user->id) // Exclude supervisor sendiri
            ->orderBy('name')
            ->get(['id', 'name', 'divisi']);

        $data = [
            'employees' => $employees
        ];

        return view('hr.overtime.create', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'request_date' => 'required|date',
            'location' => 'required|string|max:255',
            'employee_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'job_description' => 'required|string',
        ]);
        $user = Auth::user();
        $entry = OvertimeEntry::create(array_merge($data, [
            'employee_id' => $user->id,
            'divisi_id' => $user->divisi,
            'status' => OvertimeEntry::STATUS_PENDING_SPV,
        ]));
        return redirect()->route('hr.overtime.index')->with('success', 'Data lembur tersimpan.');
    }

    // SPV Rekap (by divisi)
    public function spvPending(Request $request)
    {
        $user = Auth::user();

        // Authorization: Hanya SPV yang bisa akses (jabatan = 5)
        if ($user->jabatan != 5) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya SPV yang dapat mengakses.');
        }

        // Get employee IDs in same division
        $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

        // Get approval flows untuk menentukan urutan approval
        // Untuk absence, gunakan divisi user untuk mendapatkan approval flow yang sesuai
        $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence', $user->divisi);
        $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');

        // Cek apakah SPV ada di urutan approval untuk absence dan shift_change
        $spvCanApproveAbsence = false;
        $spvCanApproveShiftChange = false;
        $spvApprovalOrderAbsence = null;
        $spvApprovalOrderShiftChange = null;

        foreach ($absenceFlow as $setting) {
            if ($setting->role_key === 'spv_division' && $setting->isUserAllowedToApprove($user)) {
                $spvCanApproveAbsence = true;
                $spvApprovalOrderAbsence = $setting->approval_order;
                break;
            }
        }

        foreach ($shiftChangeFlow as $setting) {
            if ($setting->role_key === 'spv_division' && $setting->isUserAllowedToApprove($user)) {
                $spvCanApproveShiftChange = true;
                $spvApprovalOrderShiftChange = $setting->approval_order;
                break;
            }
        }

        // Query Form Karyawan berdasarkan supervisor_approved_at yang NULL
        // Tapi harus sesuai dengan urutan approval di ApprovalSetting
        $formQuery = \App\Models\EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
            ->whereIn('employee_id', $employeeIds)
            ->whereNull('supervisor_approved_at')
            ->whereNull('supervisor_rejected_at')
            ->where(function($q) use ($spvApprovalOrderAbsence, $spvApprovalOrderShiftChange) {
                // Jika SPV di urutan 1: supervisor_approved_at NULL dan head_approved_at NULL
                // Jika SPV di urutan 2: supervisor_approved_at NULL tapi head_approved_at sudah ada
                $q->where(function($subQ) use ($spvApprovalOrderAbsence) {
                    if ($spvApprovalOrderAbsence == 1) {
                        // SPV di urutan 1: belum ada approval sama sekali
                        $subQ->where('request_type', 'absence')
                            ->whereNull('head_approved_at')
                            ->whereNull('head_rejected_at');
                    } elseif ($spvApprovalOrderAbsence == 2) {
                        // SPV di urutan 2: sudah diapprove HEAD
                        $subQ->where('request_type', 'absence')
                            ->whereNotNull('head_approved_at');
                    }
                })->orWhere(function($subQ) use ($spvApprovalOrderShiftChange) {
                    if ($spvApprovalOrderShiftChange == 1) {
                        // SPV di urutan 1: belum ada approval sama sekali
                        $subQ->where('request_type', 'shift_change')
                            ->whereNull('head_approved_at')
                            ->whereNull('head_rejected_at');
                    } elseif ($spvApprovalOrderShiftChange == 2) {
                        // SPV di urutan 2: sudah diapprove HEAD
                        $subQ->where('request_type', 'shift_change')
                            ->whereNotNull('head_approved_at');
                    }
                });
            });

        $formRequests = $formQuery->get();

        // Query Overtime berdasarkan spv_at yang NULL
        $overtimeQuery = OvertimeEntry::where('divisi_id', $user->divisi)
            ->whereNull('spv_at')
            ->whereNull('spv_rejected_at');

        // Cek apakah SPV ada di urutan approval untuk overtime
        $overtimeFlow = \App\Models\ApprovalSetting::getApprovalFlow('overtime');
        $spvCanApproveOvertime = false;
        $spvApprovalOrderOvertime = null;

        foreach ($overtimeFlow as $setting) {
            if ($setting->role_key === 'spv_division' && $setting->isUserAllowedToApprove($user)) {
                $spvCanApproveOvertime = true;
                $spvApprovalOrderOvertime = $setting->approval_order;
                break;
            }
        }

        if ($spvApprovalOrderOvertime == 2) {
            // SPV di urutan 2: sudah diapprove HEAD
            $overtimeQuery->whereNotNull('head_at');
        } elseif ($spvApprovalOrderOvertime == 1) {
            // SPV di urutan 1: belum ada approval HEAD
            $overtimeQuery->whereNull('head_at');
        }

        $overtimeRequests = $spvCanApproveOvertime ? $overtimeQuery->get() : collect();

        // Query Vehicle/Asset berdasarkan manager_at yang NULL (untuk VehicleAssetRequest)
        $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

        $vehicleRequests = $vehicleQuery->get();

        $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

        $assetRequests = $assetQuery->get();

        return view('hr.overtime.spv-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests'));
    }

    public function spvApprove(Request $request, int $id)
    {
        $user = Auth::user();
        abort_unless($user->canApprove() || (int)($user->jabatan ?? 0) === 5, 403);
        $entry = OvertimeEntry::findOrFail($id);
        DB::connection('pgsql2')->transaction(function () use ($entry, $user, $request) {
            $entry->update([
                'status' => OvertimeEntry::STATUS_SPV_APPROVED,
                'spv_id' => $user->id,
                'spv_notes' => $request->input('notes'),
                'spv_at' => now(),
            ]);
        });
        return back()->with('success', 'Disetujui SPV.');
    }

    public function spvReject(Request $request, int $id)
    {
        $user = Auth::user();
        abort_unless($user->canApprove() || (int)($user->jabatan ?? 0) === 5, 403);
        $entry = OvertimeEntry::findOrFail($id);
        DB::connection('pgsql2')->transaction(function () use ($entry, $user, $request) {
            $entry->update([
                'status' => OvertimeEntry::STATUS_SPV_REJECTED,
                'spv_id' => $user->id,
                'spv_notes' => $request->input('notes'),
                'spv_at' => now(),
            ]);
        });
        return back()->with('success', 'Ditolak SPV.');
    }

    public function spvBulkApprove(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->canApprove(), 403);
        $ids = $request->input('ids', []);
        DB::connection('pgsql2')->transaction(function () use ($ids, $user, $request) {
            OvertimeEntry::whereIn('id', $ids)->update([
                'status' => OvertimeEntry::STATUS_SPV_APPROVED,
                'spv_id' => $user->id,
                'spv_notes' => $request->input('notes'),
                'spv_at' => now(),
            ]);
        });
        return back()->with('success', 'Semua diproses SPV.');
    }

    // Head Approval
    public function headPending(Request $request)
    {
        $user = Auth::user();

        // Authorization: Hanya HEAD yang bisa akses (jabatan = 4)
        if ($user->jabatan != 4) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini. Hanya HEAD yang dapat mengakses.');
        }

        // Get employee IDs in same division
        $employeeIds = \App\Models\User::where('divisi', $user->divisi)->pluck('id');

        // Get approval flows untuk menentukan urutan approval
        // Untuk absence, gunakan divisi user untuk mendapatkan approval flow yang sesuai
        $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence', $user->divisi);
        $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');

        // Cek apakah HEAD ada di urutan approval untuk absence dan shift_change
        $headCanApproveAbsence = false;
        $headCanApproveShiftChange = false;
        $headApprovalOrderAbsence = null;
        $headApprovalOrderShiftChange = null;

        foreach ($absenceFlow as $setting) {
            if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user)) {
                $headCanApproveAbsence = true;
                $headApprovalOrderAbsence = $setting->approval_order;
                break;
            }
        }

        foreach ($shiftChangeFlow as $setting) {
            if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user)) {
                $headCanApproveShiftChange = true;
                $headApprovalOrderShiftChange = $setting->approval_order;
                break;
            }
        }

        // Query Form Karyawan berdasarkan head_approved_at yang NULL
        // Tapi harus sesuai dengan urutan approval di ApprovalSetting
        $formQuery = \App\Models\EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
            ->whereIn('employee_id', $employeeIds)
            ->whereNull('head_approved_at')
            ->whereNull('head_rejected_at')
            ->where(function($q) use ($headApprovalOrderAbsence, $headApprovalOrderShiftChange) {
                // Jika HEAD di urutan 1: head_approved_at NULL dan supervisor_approved_at NULL
                // Jika HEAD di urutan 2: head_approved_at NULL tapi supervisor_approved_at sudah ada
                $q->where(function($subQ) use ($headApprovalOrderAbsence) {
                    if ($headApprovalOrderAbsence == 1) {
                        // HEAD di urutan 1: belum ada approval sama sekali
                        $subQ->where('request_type', 'absence')
                            ->whereNull('supervisor_approved_at')
                            ->whereNull('supervisor_rejected_at');
                    } elseif ($headApprovalOrderAbsence == 2) {
                        // HEAD di urutan 2: sudah diapprove SPV
                        $subQ->where('request_type', 'absence')
                            ->whereNotNull('supervisor_approved_at');
                    }
                })->orWhere(function($subQ) use ($headApprovalOrderShiftChange) {
                    if ($headApprovalOrderShiftChange == 1) {
                        // HEAD di urutan 1: belum ada approval sama sekali
                        $subQ->where('request_type', 'shift_change')
                            ->whereNull('supervisor_approved_at')
                            ->whereNull('supervisor_rejected_at');
                    } elseif ($headApprovalOrderShiftChange == 2) {
                        // HEAD di urutan 2: sudah diapprove SPV
                        $subQ->where('request_type', 'shift_change')
                            ->whereNotNull('supervisor_approved_at');
                    }
                });
            });

        $formRequests = $formQuery->get();

        // Overtime: skip untuk sekarang, sistemnya nanti sendiri
        $overtimeRequests = collect();

        // Query Vehicle/Asset berdasarkan manager_at yang NULL (untuk VehicleAssetRequest)
        $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

        $vehicleRequests = $vehicleQuery->get();

        $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
            ->where('divisi_id', $user->divisi)
            ->whereNull('manager_at')
            ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

        $assetRequests = $assetQuery->get();

        // Debug: Log untuk melihat apa yang dikembalikan
        Log::info('HEAD Pending - Form requests: ' . $formRequests->count());
        Log::info('HEAD Pending - Vehicle requests: ' . $vehicleRequests->count());
        Log::info('HEAD Pending - Asset requests: ' . $assetRequests->count());

        return view('hr.overtime.head-pending', compact('formRequests', 'overtimeRequests', 'vehicleRequests', 'assetRequests'));
    }

    public function headApprove(Request $request, int $id)
    {
        $user = Auth::user();
        abort_unless($user->canApprove(), 403);
        $entry = OvertimeEntry::findOrFail($id);
        DB::connection('pgsql2')->transaction(function () use ($entry, $user, $request) {
            $entry->update([
                'status' => OvertimeEntry::STATUS_HEAD_APPROVED,
                'head_id' => $user->id,
                'head_notes' => $request->input('notes'),
                'head_at' => now(),
            ]);
        });
        return back()->with('success', 'Disetujui Head.');
    }

    public function headBulkApprove(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->canApprove(), 403);
        $ids = $request->input('ids', []);
        DB::connection('pgsql2')->transaction(function () use ($ids, $user, $request) {
            OvertimeEntry::whereIn('id', $ids)->update([
                'status' => OvertimeEntry::STATUS_HEAD_APPROVED,
                'head_id' => $user->id,
                'head_notes' => $request->input('notes'),
                'head_at' => now(),
            ]);
        });
        return back()->with('success', 'Semua disetujui Head.');
    }

    // HRGA Approval
    public function hrgaPending(Request $request)
    {
        $user = Auth::user();

        // Get pending requests using ApprovalService
        $pendingRequests = ApprovalService::getPendingRequestsForUser($user, 'overtime')
            ->filter(function($request) {
                return $request instanceof OvertimeEntry
                    && $request->status === OvertimeEntry::STATUS_HEAD_APPROVED;
            });

        // Convert to paginated collection
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $items = $pendingRequests->forPage($currentPage, $perPage);
        $entries = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $pendingRequests->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('hr.overtime.hrga-pending', compact('entries'));
    }

    public function hrgaApprove(Request $request, int $id)
    {
        $user = Auth::user();
        abort_unless($user->isHR(), 403);
        $entry = OvertimeEntry::findOrFail($id);
        DB::connection('pgsql2')->transaction(function () use ($entry, $user, $request) {
            $entry->update([
                'status' => OvertimeEntry::STATUS_HRGA_APPROVED,
                'hrga_id' => $user->id,
                'hrga_notes' => $request->input('notes'),
                'hrga_at' => now(),
            ]);
        });
        return back()->with('success', 'Final disetujui HRGA.');
    }

    public function hrgaApproved(Request $request)
    {
        $entries = OvertimeEntry::query()
            ->where('status', OvertimeEntry::STATUS_HRGA_APPROVED)
            ->orderByDesc('request_date')
            ->paginate(20);
        return view('hr.overtime.hrga-approved', compact('entries'));
    }
}
