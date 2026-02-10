<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingRegistrationController extends Controller
{
    /**
     * Display available trainings for registration
     */
    public function index(Request $request)
    {
        $employee = Auth::user();

        $query = TrainingMaster::published()
            ->active()
            ->with(['creator', 'participants' => function($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            }]);

        // Filter by department
        if ($request->has('department_id') && $request->department_id !== '') {
            $query->forDepartment($request->department_id);
        }

        // Filter by training type
        if ($request->has('training_type') && $request->training_type !== '') {
            $query->where('training_type', $request->training_type);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('training_name', 'like', "%{$search}%")
                  ->orWhere('training_code', 'like', "%{$search}%");
            });
        }

        $trainings = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get user's training history
        $userTrainings = TrainingParticipant::where('employee_id', $employee->id)
            ->with(['training.creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hr.training.registration.index', compact('trainings', 'userTrainings'));
    }

    /**
     * Show training details for registration
     */
    public function show($id)
    {
        $training = TrainingMaster::with(['creator'])->findOrFail($id);
        $employee = Auth::user();

        // Check if employee can register
        $canRegister = $training->canEmployeeRegister($employee->id);

        // Check if employee already registered
        $existingParticipant = $training->participants()
            ->where('employee_id', $employee->id)
            ->first();

        // Get participants count
        $participantsCount = $training->participants()->count();
        $approvedCount = $training->approvedParticipants()->count();

        return view('hr.training.registration.show', compact(
            'training',
            'employee',
            'canRegister',
            'existingParticipant',
            'participantsCount',
            'approvedCount'
        ));
    }

    /**
     * Register for training
     */
    public function register(Request $request, $id)
    {
        // dd($request->all());
        $training = TrainingMaster::findOrFail($id);
        $employee = Auth::user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'registration_type' => 'required|in:voluntary,recommended',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if employee can register
        if (!$training->canEmployeeRegister($employee->id)) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mendaftar untuk training ini.');
        }

        DB::beginTransaction();
        try {
            // Create registration
            $participant = TrainingParticipant::create([
                'training_id' => $training->id,
                'employee_id' => $employee->id,
                'registration_status' => 'registered',
                'registration_type' => $request->registration_type,
                'registered_at' => now(),
                'notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->route('hr.training.registration.show', $training->id)
                ->with('success', 'Pendaftaran berhasil. Menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel registration
     */
    public function cancel($id)
    {
        $participant = TrainingParticipant::where('employee_id', Auth::id())
            ->findOrFail($id);

        // Check if can be cancelled
        if (!in_array($participant->registration_status, ['registered', 'approved'])) {
            return redirect()->back()
                ->with('error', 'Pendaftaran tidak dapat dibatalkan.');
        }

        $participant->update([
            'registration_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan oleh peserta'
        ]);

        return redirect()->back()
            ->with('success', 'Pendaftaran berhasil dibatalkan.');
    }

    /**
     * Get user's training history
     */
    public function history(Request $request)
    {
        $employee = Auth::user();

        $query = TrainingParticipant::where('employee_id', $employee->id)
            ->with(['training.creator']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('registration_status', $request->status);
        }

        // Filter by training type
        if ($request->has('training_type') && $request->training_type !== '') {
            $query->whereHas('training', function($q) use ($request) {
                $q->where('training_type', $request->training_type);
            });
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('training', function($q) use ($search) {
                $q->where('training_name', 'like', "%{$search}%")
                  ->orWhere('training_code', 'like', "%{$search}%");
            });
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('hr.training.registration.history', compact('participants'));
    }

    /**
     * Get training statistics for user
     */
    public function statistics()
    {
        $employee = Auth::user();

        $stats = [
            'total_registered' => TrainingParticipant::where('employee_id', $employee->id)->count(),
            'total_completed' => TrainingParticipant::where('employee_id', $employee->id)
                ->where('registration_status', 'completed')->count(),
            'total_certificates' => TrainingParticipant::where('employee_id', $employee->id)
                ->where('certificate_issued', true)->count(),
            'pending_approval' => TrainingParticipant::where('employee_id', $employee->id)
                ->where('registration_status', 'registered')->count(),
            'approved_pending' => TrainingParticipant::where('employee_id', $employee->id)
                ->where('registration_status', 'approved')->count()
        ];

        // Get recent trainings
        $recentTrainings = TrainingParticipant::where('employee_id', $employee->id)
            ->with(['training.creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get training by type
        $trainingByType = TrainingParticipant::where('employee_id', $employee->id)
            ->with('training')
            ->get()
            ->groupBy('training.training_type')
            ->map(function($group) {
                return $group->count();
            });

        return view('hr.training.registration.statistics', compact('stats', 'recentTrainings', 'trainingByType'));
    }
}
