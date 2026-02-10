<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\TrainingMaster;
use App\Models\TrainingSchedule;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrainingValidationController extends Controller
{
    /**
     * Display training validation dashboard
     */
    public function index()
    {
        // Get completed training schedules
        $completedSchedules = TrainingSchedule::with(['training'])
            ->where('status', 'completed')
            ->orderBy('schedule_date', 'desc')
            ->paginate(10);

        // Get upcoming schedules that need validation
        $upcomingSchedules = TrainingSchedule::with(['training'])
            ->where('status', 'scheduled')
            ->where('schedule_date', '<=', now()->addDays(1))
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('main.hr.training-validation.index', compact('completedSchedules', 'upcomingSchedules'));
    }

    /**
     * Show attendance validation for a specific training schedule
     */
    public function show($scheduleId)
    {
        $schedule = TrainingSchedule::with(['training'])
            ->findOrFail($scheduleId);

        // Get all participants for this training
        $participants = TrainingParticipant::where('training_id', $schedule->training_id)
            ->with(['employee'])
            ->get();

        return view('main.hr.training-validation.show', compact('schedule', 'participants'));
    }

    /**
     * Validate attendance for a training schedule
     */
    public function validateAttendance(Request $request, $scheduleId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent',
            'notes' => 'nullable|string|max:1000'
        ]);

        $schedule = TrainingSchedule::findOrFail($scheduleId);

        DB::beginTransaction();
        try {
            // Update attendance for each participant
            foreach ($request->attendance as $participantId => $status) {
                $participant = TrainingParticipant::findOrFail($participantId);

                $participant->update([
                    'attendance_status' => $status,
                    'attended_at' => $status === 'present' ? now() : null,
                    'instructor_notes' => $request->notes ?? null
                ]);
            }

            // Update schedule status to completed
            $schedule->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            DB::commit();

            return redirect()->route('hr.training-validation.index')
                ->with('success', 'Kehadiran berhasil divalidasi dan training ditandai sebagai selesai.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memvalidasi kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Show reschedule form for absent participants
     */
    public function rescheduleForm($scheduleId)
    {
        $schedule = TrainingSchedule::with(['training'])->findOrFail($scheduleId);

        // Get participants who were absent
        $absentParticipants = TrainingParticipant::where('training_id', $schedule->training_id)
            ->where('attendance_status', 'absent')
            ->with(['employee'])
            ->get();

        return view('main.hr.training-validation.reschedule', compact('schedule', 'absentParticipants'));
    }

    /**
     * Create reschedule for absent participants
     */
    public function reschedule(Request $request, $scheduleId)
    {
        $request->validate([
            'new_schedule_date' => 'required|date|after:today',
            'new_start_time' => 'required',
            'new_end_time' => 'required|after:new_start_time',
            'new_location' => 'required|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:tb_training_participants,id'
        ]);

        $originalSchedule = TrainingSchedule::with(['training'])->findOrFail($scheduleId);

        DB::beginTransaction();
        try {
            // Create new schedule for absent participants
            $newSchedule = TrainingSchedule::create([
                'training_id' => $originalSchedule->training_id,
                'schedule_date' => $request->new_schedule_date,
                'start_time' => $request->new_start_time,
                'end_time' => $request->new_end_time,
                'location' => $request->new_location,
                'description' => 'Reschedule untuk peserta yang tidak hadir - ' . $originalSchedule->description,
                'status' => 'scheduled',
                'created_by' => auth()->id()
            ]);

            // Update participants to new schedule
            foreach ($request->participants as $participantId) {
                $participant = TrainingParticipant::findOrFail($participantId);
                $participant->update([
                    'training_id' => $originalSchedule->training_id, // Keep same training
                    'registration_status' => 'approved', // Reset status
                    'attendance_status' => null, // Reset attendance
                    'attended_at' => null,
                    'notes' => 'Reschedule dari training sebelumnya'
                ]);
            }

            DB::commit();

            return redirect()->route('hr.training-validation.index')
                ->with('success', 'Reschedule berhasil dibuat untuk ' . count($request->participants) . ' peserta yang tidak hadir.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat membuat reschedule: ' . $e->getMessage());
        }
    }

    /**
     * Get training statistics
     */
    public function statistics()
    {
        $totalTrainings = TrainingMaster::count();
        $completedTrainings = TrainingSchedule::where('status', 'completed')->count();
        $scheduledTrainings = TrainingSchedule::where('status', 'scheduled')->count();

        // Get attendance statistics
        $attendanceStats = DB::table('tb_training_participants')
            ->select('attendance_status', DB::raw('count(*) as count'))
            ->whereNotNull('attendance_status')
            ->groupBy('attendance_status')
            ->get();

        return view('main.hr.training-validation.statistics', compact(
            'totalTrainings',
            'completedTrainings',
            'scheduledTrainings',
            'attendanceStats'
        ));
    }

    /**
     * Mark training as completed (manual completion)
     */
    public function markCompleted(Request $request, $scheduleId)
    {
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000'
        ]);

        $schedule = TrainingSchedule::findOrFail($scheduleId);

        $schedule->update([
            'status' => 'completed',
            'completed_at' => now(),
            'description' => $schedule->description . ' | Completed: ' . ($request->completion_notes ?? 'Manual completion')
        ]);

        return redirect()->route('hr.training-validation.index')
            ->with('success', 'Training berhasil ditandai sebagai selesai.');
    }
}
