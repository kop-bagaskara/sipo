<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    /**
     * Handle barcode redirect - show form with pre-filled employee data
     */
    public function showFormFromBarcode(Request $request)
    {
        try {
            $employeeId = $request->get('id');

            // dd($employeeId);

            if (!$employeeId) {
                return redirect()->route('public.leave-request.error')
                    ->with('error', 'ID karyawan tidak ditemukan dalam barcode. Pastikan barcode valid dan tidak rusak.');
            }

            // Find employee by ID
            $employee = DB::connection('mysql7')->table('masteremployee')->where('Nip', $employeeId)
                // ->where('Aktif', 1)
                ->leftJoin('masterdivisi', 'masteremployee.Kode Divisi', '=', 'masterdivisi.Kode Divisi')
                ->leftJoin('masterbagian', 'masteremployee.Kode Bagian', '=', 'masterbagian.Kode Bagian')
                ->select('masteremployee.Nip', 'masteremployee.Nama', 'masteremployee.Kode Divisi', 'masterdivisi.Nama Divisi as DivisiNama', 'masteremployee.Kode Bagian', 'masterbagian.Nama Bagian as BagianNama', 'masteremployee.Email', 'masteremployee.No Telp', 'masteremployee.Begda', 'masteremployee.Endda')
                ->where('masteremployee.Begda', '<=', now())
                ->where(function($query) {
                    $query->whereNull('masteremployee.Endda')
                          ->orWhere('masteremployee.Endda', '>=', now());
                })
                ->orderBy('masteremployee.Begda', 'desc')
                ->first();

            if (!$employee) {
                return redirect()->route('public.leave-request.error')
                    ->with('error', 'Data karyawan tidak ditemukan atau status tidak aktif. Silakan hubungi HR untuk konfirmasi.');
            }

            return view('hr.leave-request.form', compact('employee'));
        } catch (\Exception $e) {
            Log::error("Error in showFormFromBarcode: " . $e->getMessage());
            return redirect()->route('public.leave-request.error')
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi tim IT.');
        }
    }

    /**
     * Show error page for invalid barcode
     */
    public function showError()
    {
        return view('hr.leave-request.error');
    }

    /**
     * Get employee data for auto-fill
     */
    public function getEmployeeData($id)
    {
        try {
            $employee = DB::connection('mysql7')->table('masteremployee')
                ->where('Nip', $id)
                ->where('Aktif', 1)
                ->where('Begda', '<=', now())
                ->where(function($query) {
                    $query->whereNull('Endda')
                          ->orWhere('Endda', '>=', now());
                })
                ->orderBy('Begda', 'desc')
                ->first();

            // dd($employee);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan atau status tidak aktif'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->Nip,
                    'nama_lengkap' => $employee->Nama,
                    'email' => $employee->Email ?? '',
                    'posisi' => $employee->Posisi ?? '',
                    'divisi' => $employee->Divisi ?? '',
                    'no_hp' => $employee->No_hp ?? '',
                    'status' => $employee->Status ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching employee data', [
                'error' => $e->getMessage(),
                'employee_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mengambil data karyawan'
            ], 500);
        }
    }

    /**
     * Store leave request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:tb_applicants,id',
            'leave_type' => 'required|string|in:sick,personal,annual,emergency',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'contact_during_leave' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create leave request record in database
            $leaveRequestId = DB::connection('pgsql2')->table('leave_requests')->insertGetId([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'contact_during_leave' => $request->contact_during_leave,
                'emergency_contact' => $request->emergency_contact,
                'status' => 'pending',
                'submitted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Leave request submitted', [
                'employee_id' => $request->employee_id,
                'leave_request_id' => $leaveRequestId,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            return redirect()->route('hr.leave-request.success', $leaveRequestId)
                ->with('success', 'Pengajuan cuti berhasil dikirim');
        } catch (\Exception $e) {
            Log::error('Leave request submission failed', [
                'error' => $e->getMessage(),
                'employee_id' => $request->employee_id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Show success page
     */
    public function showSuccess($id)
    {
        try {
            // Get leave request data
            $leaveRequest = DB::connection('pgsql2')->table('leave_requests')
                ->where('id', $id)
                ->first();

            if (!$leaveRequest) {
                return redirect()->route('hr.leave-request.error')
                    ->with('error', 'Data pengajuan tidak ditemukan');
            }

            // Get employee data
            $employee = Applicant::find($leaveRequest->employee_id);

            if (!$employee) {
                return redirect()->route('hr.leave-request.error')
                    ->with('error', 'Data karyawan tidak ditemukan');
            }

            // Format data for view
            $leaveRequestData = (object) [
                'id' => $leaveRequest->id,
                'leave_type' => $leaveRequest->leave_type,
                'start_date' => \Carbon\Carbon::parse($leaveRequest->start_date),
                'end_date' => \Carbon\Carbon::parse($leaveRequest->end_date),
                'reason' => $leaveRequest->reason,
                'status' => $leaveRequest->status,
                'submitted_at' => \Carbon\Carbon::parse($leaveRequest->submitted_at),
                'employee' => $employee,
                'leave_type_formatted' => $this->getLeaveTypeFormatted($leaveRequest->leave_type),
                'status_formatted' => $this->getStatusFormatted($leaveRequest->status),
                'duration_days' => \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1
            ];

            return view('hr.leave-request.success', ['leaveRequest' => $leaveRequestData]);
        } catch (\Exception $e) {
            Log::error('Error showing success page', [
                'error' => $e->getMessage(),
                'leave_request_id' => $id
            ]);

            return redirect()->route('hr.leave-request.error')
                ->with('error', 'Terjadi kesalahan saat menampilkan data');
        }
    }

    /**
     * Get formatted leave type
     */
    private function getLeaveTypeFormatted($type)
    {
        $types = [
            'sick' => 'Cuti Sakit',
            'personal' => 'Cuti Pribadi',
            'annual' => 'Cuti Tahunan',
            'emergency' => 'Cuti Darurat'
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Get formatted status
     */
    private function getStatusFormatted($status)
    {
        $statuses = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];

        return $statuses[$status] ?? $status;
    }
}
