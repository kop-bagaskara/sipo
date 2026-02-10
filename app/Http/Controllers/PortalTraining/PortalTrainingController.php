<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaterialProgress;
use App\Models\TrainingResult;

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
}
