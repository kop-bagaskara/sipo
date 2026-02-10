<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingParticipant;
use App\Models\PaytestEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingManagementController extends Controller
{
    /**
     * Display training management page
     */
    public function index(Request $request)
    {
        $trainings = TrainingMaster::with(['creator', 'participants'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get departments from paytest (using Kode Divisi)
        $departments = PaytestEmployee::select('Kode Divisi')
            ->distinct()
            ->whereNotNull('Kode Divisi')
            ->orderBy('Kode Divisi')
            ->pluck('Kode Divisi');

        return view('hr.training.management.index', compact('trainings', 'departments'));
    }

    /**
     * Show training details and employee management
     */
    public function show($id)
    {
        $training = TrainingMaster::with(['creator', 'participants.employee'])
            ->findOrFail($id);

        // Get participants from paytest employees
        $participants = TrainingParticipant::where('training_id', $id)
            ->with(['employee' => function($query) {
                $query->select('Nip', 'Nama', 'Email', 'Kode Divisi', 'Kode Bagian', 'Kode Jabatan');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get departments from paytest (using Kode Divisi)
        $departments = PaytestEmployee::select('Kode Divisi')
            ->distinct()
            ->whereNotNull('Kode Divisi')
            ->orderBy('Kode Divisi')
            ->pluck('Kode Divisi');

        return view('hr.training.management.show', compact('training', 'participants', 'departments'));
    }

    /**
     * Get employees from paytest database
     */
    public function getEmployees(Request $request)
    {
        $query = DB::connection('mysql7')->table('masteremployee')
            ->select('Nip', 'Nama', 'Email', 'Kode Divisi', 'Kode Bagian', 'Kode Jabatan');

        // Filter by division (Kode Divisi)
        if ($request->has('department') && $request->department !== '') {
            $query->where('Kode Divisi', $request->department);
        }

        // Filter active employees based on Begda and Endda
        $today = now()->format('Y-m-d');
        $query->where('Begda', '<=', $today)
              ->where(function($q) use ($today) {
                  $q->whereNull('Endda')
                    ->orWhere('Endda', '>=', $today);
              });

        // Search by name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Nama', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%")
                  ->orWhere('Nip', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('Nama')
            ->paginate(20);

        return response()->json($employees);
    }

    /**
     * Register employees to training
     */
    public function registerEmployees(Request $request, $trainingId)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            // 'employee_ids.*' => 'exists:masteremployee,Nip',
            // 'registration_type' => 'required|in:mandatory,voluntary,recommended'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $training = TrainingMaster::findOrFail($trainingId);
        // dd($training);
        $registeredCount = 0;
        $alreadyRegistered = 0;

        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $employeeId) {
                // Check if employee already registered
                $existingParticipant = TrainingParticipant::where('training_id', $trainingId)
                    ->where('employee_id', $employeeId)
                    ->first();

                if ($existingParticipant) {
                    $alreadyRegistered++;
                    continue;
                }

                // Register employee (langsung approved karena HRD yang daftarkan)
                TrainingParticipant::create([
                    'training_id' => $trainingId,
                    'employee_id' => $employeeId,
                    'registration_status' => 'approved',
                    'registration_type' => $training->training_type,
                    'registered_at' => now(),
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'notes' => 'Didaftarkan dan disetujui oleh HR'
                ]);

                $registeredCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mendaftarkan {$registeredCount} karyawan. {$alreadyRegistered} karyawan sudah terdaftar sebelumnya.",
                'registered_count' => $registeredCount,
                'already_registered' => $alreadyRegistered
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove employee from training
     */
    public function removeEmployee(Request $request, $trainingId, $participantId)
    {
        $participant = TrainingParticipant::where('training_id', $trainingId)
            ->findOrFail($participantId);

        // Check if can be removed
        if (!in_array($participant->registration_status, ['registered', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak dapat dihapus karena sudah dalam status yang tidak memungkinkan.'
            ], 400);
        }

        $participant->update([
            'registration_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Dihapus oleh HR'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dihapus dari training.'
        ]);
    }

    /**
     * Bulk approve participants
     */
    public function bulkApprove(Request $request, $trainingId)
    {
        $validator = Validator::make($request->all(), [
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:tb_training_participants,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $approvedCount = 0;

            foreach ($request->participant_ids as $participantId) {
                $participant = TrainingParticipant::where('training_id', $trainingId)
                    ->findOrFail($participantId);

                if ($participant->registration_status === 'registered') {
                    $participant->update([
                        'registration_status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => Auth::id()
                    ]);
                    $approvedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui {$approvedCount} peserta.",
                'approved_count' => $approvedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
