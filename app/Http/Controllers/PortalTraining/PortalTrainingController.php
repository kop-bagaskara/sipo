<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaterial;
use App\Models\TrainingMaterialProgress;
use App\Models\TrainingMaster;
use App\Models\TrainingQuestionBank;
use App\Models\TrainingResult;
use App\Models\TrainingSession;
use App\Models\TrainingSessionProgress;
use App\Services\GoogleDriveService;

class PortalTrainingController extends Controller
{
    /**
     * Display portal training dashboard for employee
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $employeeId = $user->id; // Sesuaikan dengan field employee_id di tabel user

        // Ambil semua assignment untuk user yang sedang login
        $assignments = TrainingAssignment::with(['training', 'materials'])
            ->where('employee_id', $employeeId)
            ->orderBy('assigned_date', 'desc')
            ->get();

        // Pastikan materials ter-load dengan benar
        foreach ($assignments as $assignment) {
            // Jika materials kosong dari pivot, coba load dari material_ids JSON sebagai fallback
            if ($assignment->materials->isEmpty() && $assignment->material_ids) {
                $materialIds = is_array($assignment->material_ids)
                    ? $assignment->material_ids
                    : json_decode($assignment->material_ids, true);

                if (!empty($materialIds)) {
                    // Attach materials ke pivot table jika belum ada
                    $assignment->materials()->sync($materialIds);
                    // Reload materials
                    $assignment->load('materials');
                }
            }
        }

        // Update progress untuk setiap assignment
        foreach ($assignments as $assignment) {
            $assignment->progress_percentage = $this->calculateProgress($assignment->id);
            $assignment->save();
        }

        return view('portal-training.index', compact('assignments'));
    }

    /**
     * Calculate progress percentage for an assignment
     *
     * @param int $assignmentId
     * @return float
     */
    private function calculateProgress($assignmentId)
    {
        $totalMaterials = TrainingMaterialProgress::where('assignment_id', $assignmentId)->count();
        if ($totalMaterials == 0) {
            return 0;
        }

        $completedMaterials = TrainingMaterialProgress::where('assignment_id', $assignmentId)
            ->where('status', 'completed')
            ->count();

        return ($completedMaterials / $totalMaterials) * 100;
    }

    /**
     * Get training history/results for the logged in user
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $user = Auth::user();
        $employeeId = $user->id;

        // Ambil hasil training yang sudah selesai
        $results = TrainingResult::with(['assignment.training'])
            ->where('employee_id', $employeeId)
            ->orderBy('completed_date', 'desc')
            ->paginate(10);

        return view('portal-training.history', compact('results'));
    }

    /**
     * Master Dashboard for Portal Training Admin
     *
     * @return \Illuminate\View\View
     */
    public function masterDashboard()
    {
        $user = Auth::user();

        // Statistik Training Assignments
        $totalAssignments = TrainingAssignment::count();
        $assignedCount = TrainingAssignment::where('status', TrainingAssignment::STATUS_ASSIGNED)->count();
        $inProgressCount = TrainingAssignment::where('status', TrainingAssignment::STATUS_IN_PROGRESS)->count();
        $completedCount = TrainingAssignment::where('status', TrainingAssignment::STATUS_COMPLETED)->count();
        $expiredCount = TrainingAssignment::where('status', TrainingAssignment::STATUS_EXPIRED)->count();

        // Statistik Training Master
        $totalTrainings = TrainingMaster::where('is_active', true)->count();
        $totalSessions = TrainingSession::where('is_active', true)->count();

        // Statistik Bank Soal
        $totalQuestions = TrainingQuestionBank::where('is_active', true)->count();

        // Statistik Materi
        $totalMaterials = TrainingMaterial::where('is_active', true)->count();

        // Statistik Session Progress
        $totalSessionProgress = TrainingSessionProgress::count();
        $passedSessions = TrainingSessionProgress::where('status', TrainingSessionProgress::STATUS_PASSED)->count();
        $failedSessions = TrainingSessionProgress::where('status', TrainingSessionProgress::STATUS_FAILED)->count();
        $inProgressSessions = TrainingSessionProgress::where('status', TrainingSessionProgress::STATUS_IN_PROGRESS)->count();

        // Rata-rata score
        $averageScore = TrainingSessionProgress::whereNotNull('score')
            ->where('status', '!=', TrainingSessionProgress::STATUS_NOT_STARTED)
            ->avg('score') ?? 0;

        // Total karyawan yang di-assign
        $totalEmployees = TrainingAssignment::distinct('employee_id')->count('employee_id');

        // Training yang akan expired (dalam 7 hari)
        $upcomingExpired = TrainingAssignment::where('status', '!=', TrainingAssignment::STATUS_COMPLETED)
            ->whereNotNull('deadline_date')
            ->whereBetween('deadline_date', [now(), now()->addDays(7)])
            ->count();

        // Training yang belum dibuka
        $notOpenedCount = TrainingAssignment::where('is_opened', false)
            ->where('status', TrainingAssignment::STATUS_ASSIGNED)
            ->count();

        // Recent assignments (5 terakhir) untuk user yang sedang login
        $recentAssignments = TrainingAssignment::with(['training', 'employee'])
            ->where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top performing trainings (by completion rate)
        $topTrainings = TrainingAssignment::select('training_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed', [TrainingAssignment::STATUS_COMPLETED])
            ->whereNotNull('training_id')
            ->groupBy('training_id')
            ->havingRaw('COUNT(*) > 0')
            ->orderByRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) DESC', [TrainingAssignment::STATUS_COMPLETED])
            ->limit(5)
            ->with('training')
            ->get();

        // Training assignments untuk user yang sedang login
        $user = Auth::user();
        $myAssignments = TrainingAssignment::with(['training', 'sessionProgress.session'])
            ->where('employee_id', $user->id)
            ->orderBy('assigned_date', 'desc')
            ->get();

        // Hitung progress untuk setiap assignment
        foreach ($myAssignments as $assignment) {
            if ($assignment->training) {
                $totalSessions = $assignment->training->sessions()->active()->count();
                $completedSessions = $assignment->sessionProgress()
                    ->whereIn('status', [
                        TrainingSessionProgress::STATUS_PASSED,
                        TrainingSessionProgress::STATUS_COMPLETED,
                        TrainingSessionProgress::STATUS_FAILED
                    ])
                    ->count();
                $assignment->progress_percentage = $totalSessions > 0
                    ? ($completedSessions / $totalSessions) * 100
                    : 0;
            }
        }

        // Training yang belum dibuka oleh user
        $myNotOpenedAssignments = $myAssignments->filter(function($assignment) {
            return !$assignment->is_opened && $assignment->status === TrainingAssignment::STATUS_ASSIGNED;
        });

        // Training yang sedang dikerjakan oleh user
        $myInProgressAssignments = $myAssignments->filter(function($assignment) {
            return $assignment->status === TrainingAssignment::STATUS_IN_PROGRESS;
        });

        // Training yang akan expired (dalam 7 hari) untuk user
        $myUpcomingExpired = $myAssignments->filter(function($assignment) {
            return $assignment->status !== TrainingAssignment::STATUS_COMPLETED
                && $assignment->deadline_date
                && $assignment->deadline_date->between(now(), now()->addDays(7));
        });

        return view('portal-training.master.dashboard', compact(
            'totalAssignments',
            'assignedCount',
            'inProgressCount',
            'completedCount',
            'expiredCount',
            'totalTrainings',
            'totalSessions',
            'totalQuestions',
            'totalMaterials',
            'totalSessionProgress',
            'passedSessions',
            'failedSessions',
            'inProgressSessions',
            'averageScore',
            'totalEmployees',
            'upcomingExpired',
            'notOpenedCount',
            'recentAssignments',
            'topTrainings',
            'myAssignments',
            'myNotOpenedAssignments',
            'myInProgressAssignments',
            'myUpcomingExpired'
        ));

        return view('portal-training.master.dashboard', compact('cards'));
    }

    /**
     * View training scores/results for a specific assignment
     *
     * @param int $assignmentId
     * @return \Illuminate\View\View
     */
    public function viewScores($assignmentId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        // Ambil assignment dengan semua relasi
        $assignment = TrainingAssignment::with([
            'training.sessions' => function($query) {
                $query->active()->orderBy('session_order', 'asc');
            },
            'sessionProgress.session'
        ])
        ->where('id', $assignmentId)
        ->where('employee_id', $employeeId)
        ->firstOrFail();

        // Ambil semua session progress untuk assignment ini
        $sessionProgresses = TrainingSessionProgress::with(['session'])
            ->where('assignment_id', $assignmentId)
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung statistik
        $totalSessions = $assignment->training->sessions->count();
        $completedSessions = $sessionProgresses->whereIn('status', [
            TrainingSessionProgress::STATUS_PASSED,
            TrainingSessionProgress::STATUS_COMPLETED,
            TrainingSessionProgress::STATUS_FAILED
        ])->count();

        $passedSessions = $sessionProgresses->where('status', TrainingSessionProgress::STATUS_PASSED)->count();
        $failedSessions = $sessionProgresses->where('status', TrainingSessionProgress::STATUS_FAILED)->count();

        $totalScore = $sessionProgresses->sum('score');

        // Hitung max possible score dari questions_data (jika ada)
        $maxPossibleScore = 0;
        foreach ($sessionProgresses as $sp) {
            if ($sp->questions_data && is_array($sp->questions_data)) {
                foreach ($sp->questions_data as $q) {
                    $maxPossibleScore += $q['score'] ?? 0;
                }
            }
        }

        $averageScore = $sessionProgresses->where('score', '>', 0)->count() > 0
            ? $sessionProgresses->where('score', '>', 0)->avg('score')
            : 0;

        return view('portal-training.scores', compact(
            'assignment',
            'sessionProgresses',
            'totalSessions',
            'completedSessions',
            'passedSessions',
            'failedSessions',
            'totalScore',
            'maxPossibleScore',
            'averageScore'
        ));
    }

    /**
     * List all training scores for the logged in user
     *
     * @return \Illuminate\View\View
     */
    public function scoresIndex()
    {
        $user = Auth::user();
        $employeeId = $user->id;

        // Ambil semua assignment yang sudah ada progress
        $assignments = TrainingAssignment::with(['training', 'sessionProgress'])
            ->where('employee_id', $employeeId)
            ->whereHas('sessionProgress', function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                      ->whereIn('status', [
                          TrainingSessionProgress::STATUS_PASSED,
                          TrainingSessionProgress::STATUS_COMPLETED,
                          TrainingSessionProgress::STATUS_FAILED,
                          TrainingSessionProgress::STATUS_IN_PROGRESS
                      ]);
            })
            ->orderBy('assigned_date', 'desc')
            ->get();

        return view('portal-training.scores-index', compact('assignments'));
    }

    /**
     * Start training assignment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function start($id)
    {
        $user = Auth::user();
        $assignment = TrainingAssignment::where('id', $id)
            ->where('employee_id', $user->id)
            ->firstOrFail();

        // Validasi: hanya bisa start jika training sudah dibuka oleh penyelenggara (is_opened = true)
        if (!$assignment->is_opened) {
            return response()->json([
                'success' => false,
                'message' => 'Training belum dibuka oleh penyelenggara. Silakan tunggu penyelenggara membuka training.'
            ], 400);
        }

        // Jika status masih 'assigned', ubah ke 'in_progress' saat user mulai mengerjakan
        if ($assignment->status === TrainingAssignment::STATUS_ASSIGNED) {
            $assignment->update([
                'status' => TrainingAssignment::STATUS_IN_PROGRESS,
            ]);
        }

        // Redirect ke session pertama dari training
        $firstSession = TrainingSession::where('training_id', $assignment->training_id)
            ->active()
            ->orderBy('session_order', 'asc')
            ->first();

        if ($firstSession) {
            return response()->json([
                'success' => true,
                'message' => 'Training berhasil dimulai.',
                'redirect_url' => route('hr.portal-training.sessions.show', [$assignment->id, $firstSession->id])
            ]);
        }

        // Fallback ke materials jika tidak ada session
        return response()->json([
            'success' => true,
            'message' => 'Training berhasil dimulai.',
            'redirect_url' => route('hr.portal-training.materials.show', $assignment->materials->first()->id ?? '#')
        ]);
    }

    /**
     * Show training session
     *
     * @param int $assignmentId
     * @param int $sessionId
     * @return \Illuminate\View\View
     */
    public function showSession($assignmentId, $sessionId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        // Get assignment
        $assignment = TrainingAssignment::where('id', $assignmentId)
            ->where('employee_id', $employeeId)
            ->with('training')
            ->firstOrFail();

        // Get session
        $session = TrainingSession::where('id', $sessionId)
            ->where('training_id', $assignment->training_id)
            ->firstOrFail();

        // Check if user can access this session (must complete previous sessions first)
        if (!$session->canUserStart($employeeId, $assignment->id)) {
            // Redirect to first incomplete session or current session
            $allSessions = TrainingSession::where('training_id', $assignment->training_id)
                ->active()
                ->ordered()
                ->get();

            foreach ($allSessions as $sess) {
                if ($sess->canUserStart($employeeId, $assignment->id)) {
                    return redirect()->route('hr.portal-training.sessions.show', [$assignment->id, $sess->id])
                        ->with('error', 'Anda harus menyelesaikan sesi sebelumnya terlebih dahulu.');
                }
            }

            return redirect()->route('hr.portal-training.index')
                ->with('error', 'Tidak ada sesi yang dapat diakses.');
        }

        // Get or create session progress
        $sessionProgress = TrainingSessionProgress::where('assignment_id', $assignment->id)
            ->where('session_id', $session->id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$sessionProgress) {
            $sessionProgress = TrainingSessionProgress::create([
                'assignment_id' => $assignment->id,
                'session_id' => $session->id,
                'employee_id' => $employeeId,
                'status' => TrainingSessionProgress::STATUS_NOT_STARTED,
            ]);
        }

        // Get all sessions for this training
        $allSessions = TrainingSession::where('training_id', $assignment->training_id)
            ->active()
            ->ordered()
            ->get();

        // Get all session progress for this assignment
        $sessionProgressList = TrainingSessionProgress::where('assignment_id', $assignment->id)
            ->where('employee_id', $employeeId)
            ->get()
            ->keyBy('session_id');

        // Get next session
        $nextSession = TrainingSession::where('training_id', $session->training_id)
            ->where('session_order', '>', $session->session_order)
            ->active()
            ->orderBy('session_order', 'asc')
            ->first();

        // Check if user can start this session (always true - user can start any session)
        $canStart = $session->canUserStart($employeeId, $assignment->id);

        // Load training to check allow_retry
        $assignment->load('training');
        $allowRetry = $assignment->training ? $assignment->training->allow_retry : false;

        // Get materials for this assignment (for tracking/debugging)
        $assignmentMaterials = collect();
        if ($assignment->material_ids) {
            $materialIds = is_array($assignment->material_ids)
                ? $assignment->material_ids
                : json_decode($assignment->material_ids, true);
            if (!empty($materialIds)) {
                $assignmentMaterials = \App\Models\TrainingMaterial::whereIn('id', $materialIds)->get();
            }
        }

        // If no materials from assignment, get from training master
        if ($assignmentMaterials->isEmpty() && $assignment->training) {
            $assignmentMaterials = $assignment->training->materials;
        }

        // Get difficulty level info
        $difficultyLevel = null;
        if ($session->difficulty_level_id) {
            $difficultyLevel = \App\Models\TrainingDifficultyLevel::find($session->difficulty_level_id);
        }

        return view('portal-training.sessions.show', compact(
            'session',
            'assignment',
            'sessionProgress',
            'allSessions',
            'sessionProgressList',
            'nextSession',
            'canStart',
            'allowRetry',
            'assignmentMaterials',
            'difficultyLevel'
        ));
    }

    /**
     * Start training session
     *
     * @param Request $request
     * @param int $assignmentId
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function startSession(Request $request, $assignmentId, $sessionId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $assignment = TrainingAssignment::where('id', $assignmentId)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        $session = TrainingSession::where('id', $sessionId)
            ->where('training_id', $assignment->training_id)
            ->firstOrFail();

        $sessionProgress = TrainingSessionProgress::where('assignment_id', $assignment->id)
            ->where('session_id', $session->id)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        // Start the session
        $sessionProgress->startSession();

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil dimulai.',
            'questions' => $sessionProgress->questions_data
        ]);
    }

    /**
     * Submit session answers
     *
     * @param Request $request
     * @param int $assignmentId
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitSession(Request $request, $assignmentId, $sessionId)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $user = Auth::user();

        $assignment = TrainingAssignment::with(['training.sessions', 'sessionProgress'])
            ->where('id', $assignmentId)
            ->where('employee_id', $user->id)
            ->firstOrFail();

        $session = TrainingSession::where('id', $sessionId)
            ->where('training_id', $assignment->training_id)
            ->firstOrFail();

        $sessionProgress = TrainingSessionProgress::where('assignment_id', $assignment->id)
            ->where('session_id', $session->id)
            ->where('employee_id', $user->id)
            ->firstOrFail();

        // Submit answers
        $passed = $sessionProgress->submitAnswers($request->answers);

        // Reload assignment with relationships to check completion
        $assignment->refresh();
        $assignment->load(['training.sessions', 'sessionProgress']);

        // Check if all sessions are completed
        $allCompleted = $assignment->isAllSessionsCompleted();

        if ($allCompleted) {
            $assignment->update([
                'status' => TrainingAssignment::STATUS_COMPLETED,
                'progress_percentage' => 100.00,
            ]);

            \Illuminate\Support\Facades\Log::info("Assignment {$assignment->id} marked as completed", [
                'assignment_id' => $assignment->id,
                'employee_id' => $user->id,
                'total_sessions' => $assignment->training->sessions()->active()->count(),
                'completed_sessions' => $assignment->sessionProgress()
                    ->whereNotIn('status', [TrainingSessionProgress::STATUS_NOT_STARTED, TrainingSessionProgress::STATUS_IN_PROGRESS])
                    ->count(),
            ]);
        }

        // Get next session - user can always proceed regardless of pass/fail
        $currentSession = $sessionProgress->session;
        $nextSession = TrainingSession::where('training_id', $currentSession->training_id)
            ->where('session_order', '>', $currentSession->session_order)
            ->active()
            ->orderBy('session_order', 'asc')
            ->first();

        $nextSessionUrl = null;
        $hasNextSession = false;

        if ($nextSession) {
            try {
                // Pastikan next session bisa diakses (tidak locked)
                if ($nextSession->canUserStart($user->id, $assignment->id)) {
                    $nextSessionUrl = route('hr.portal-training.sessions.show', [$assignment->id, $nextSession->id]);
                    $hasNextSession = true;
                } else {
                    \Illuminate\Support\Facades\Log::warning("Next session is locked", [
                        'assignment_id' => $assignment->id,
                        'next_session_id' => $nextSession->id,
                        'employee_id' => $user->id
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error generating next session URL: " . $e->getMessage(), [
                    'assignment_id' => $assignment->id,
                    'next_session_id' => $nextSession->id,
                    'error' => $e->getTraceAsString()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'passed' => $passed,
            'score' => $sessionProgress->score,
            'correct_answers' => $sessionProgress->correct_answers_count,
            'total_questions' => $sessionProgress->total_questions,
            'message' => 'Jawaban telah disimpan.',
            'next_session_url' => $nextSessionUrl,
            'next_session_id' => $nextSession ? $nextSession->id : null,
            'has_next_session' => $hasNextSession,
            'next_session_locked' => $nextSession && !$hasNextSession,
        ]);
    }

    /**
     * Retry training session
     *
     * @param Request $request
     * @param int $assignmentId
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrySession(Request $request, $assignmentId, $sessionId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $assignment = TrainingAssignment::where('id', $assignmentId)
            ->where('employee_id', $employeeId)
            ->with('training')
            ->firstOrFail();

        // Cek apakah training mengizinkan retry
        if (!$assignment->training || !$assignment->training->allow_retry) {
            return response()->json([
                'success' => false,
                'message' => 'Training ini tidak mengizinkan pengulangan. Silakan hubungi administrator untuk informasi lebih lanjut.'
            ], 403);
        }

        $session = TrainingSession::where('id', $sessionId)
            ->where('training_id', $assignment->training_id)
            ->firstOrFail();

        $sessionProgress = TrainingSessionProgress::where('assignment_id', $assignment->id)
            ->where('session_id', $session->id)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        // Retry the session
        $sessionProgress->retry();

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil direset. Silakan mulai lagi.',
            'questions' => $sessionProgress->questions_data
        ]);
    }

    /**
     * Stream video from Google Drive
     * This allows full control (prevent skip, speed control) even when video is stored in Google Drive
     *
     * @param string $fileId Google Drive file ID
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function streamVideo($fileId, Request $request)
    {
        $user = Auth::user();

        // Verify that this video belongs to a session that the user has access to
        $session = TrainingSession::where('google_drive_file_id', $fileId)
            ->where('has_video', true)
            ->first();

        if (!$session) {
            abort(404, 'Video tidak ditemukan');
        }

        // Check if user has access to this session through an assignment
        $hasAccess = TrainingAssignment::whereHas('training.sessions', function($query) use ($session) {
            $query->where('id', $session->id);
        })
        ->where('employee_id', $user->id)
        ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke video ini');
        }

        // Handle range requests for video seeking
        $startByte = null;
        $endByte = null;

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $startByte = (int) $matches[1];
                $endByte = !empty($matches[2]) ? (int) $matches[2] : null;
            }
        }

        // Stream video using GoogleDriveService
        $googleDriveService = new GoogleDriveService();
        return $googleDriveService->streamVideo($fileId, $startByte, $endByte);
    }
}
