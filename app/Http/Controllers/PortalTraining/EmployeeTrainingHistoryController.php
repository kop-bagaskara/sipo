<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingAssignment;
use App\Models\TrainingSessionProgress;
use App\Models\User;
use App\Models\TrainingMaster;
use App\Models\TrainingParticipant; // HR Training
use Illuminate\Support\Facades\DB;

class EmployeeTrainingHistoryController extends Controller
{
    /**
     * Display employee training history (combining Portal Training and HR Training)
     */
    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $employee = null;
        $portalTrainings = collect();
        $hrTrainings = collect();

        if ($employeeId) {
            $employee = User::find($employeeId);
            
            if ($employee) {
                // Portal Training Assignments
                $portalTrainings = TrainingAssignment::with([
                    'training',
                    'sessionProgress.session'
                ])
                ->where('employee_id', $employeeId)
                ->orderBy('assigned_date', 'desc')
                ->get()
                ->map(function($assignment) {
                    $sessionProgresses = $assignment->sessionProgress;
                    $totalSessions = $assignment->training->sessions()->active()->count();
                    $completedSessions = $sessionProgresses->whereIn('status', [
                        TrainingSessionProgress::STATUS_PASSED,
                        TrainingSessionProgress::STATUS_COMPLETED,
                        TrainingSessionProgress::STATUS_FAILED
                    ])->count();
                    $passedSessions = $sessionProgresses->where('status', TrainingSessionProgress::STATUS_PASSED)->count();
                    $totalScore = $sessionProgresses->sum('score');
                    $averageScore = $sessionProgresses->where('score', '>', 0)->count() > 0 
                        ? $sessionProgresses->where('score', '>', 0)->avg('score') 
                        : 0;

                    return [
                        'id' => $assignment->id,
                        'type' => 'portal',
                        'training_name' => $assignment->training->training_name ?? '-',
                        'assigned_date' => $assignment->assigned_date,
                        'status' => $assignment->status,
                        'progress_percentage' => $assignment->progress_percentage ?? 0,
                        'total_sessions' => $totalSessions,
                        'completed_sessions' => $completedSessions,
                        'passed_sessions' => $passedSessions,
                        'total_score' => $totalScore,
                        'average_score' => $averageScore,
                        'start_date' => $assignment->start_date,
                        'deadline_date' => $assignment->deadline_date,
                    ];
                });

                // HR Training Participants
                $hrTrainings = TrainingParticipant::with(['training'])
                    ->where('employee_id', $employeeId)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($participant) {
                        return [
                            'id' => $participant->id,
                            'type' => 'hr',
                            'training_name' => $participant->training->training_name ?? '-',
                            'assigned_date' => $participant->created_at,
                            'status' => $participant->registration_status ?? 'registered',
                            'progress_percentage' => $participant->attendance_percentage ?? 0,
                            'total_sessions' => null,
                            'completed_sessions' => null,
                            'passed_sessions' => null,
                            'total_score' => null,
                            'average_score' => null,
                            'start_date' => $participant->training->start_date ?? null,
                            'deadline_date' => $participant->training->end_date ?? null,
                            'certificate_issued' => $participant->certificate_issued ?? false,
                        ];
                    });
            }
        }

        // Get all employees for dropdown
        $employees = User::where('is_active', true)->orderBy('name')->get();

        return view('portal-training.master.employee-history.index', compact(
            'employee',
            'portalTrainings',
            'hrTrainings',
            'employees'
        ));
    }
}

