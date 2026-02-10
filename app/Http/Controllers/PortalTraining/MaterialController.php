<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaterial;
use App\Models\TrainingMaterialProgress;
use Carbon\Carbon;

class MaterialController extends Controller
{
    /**
     * Display all materials for user's assignments
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $employeeId = $user->id;

        // Ambil semua assignment user dengan materials
        $assignments = TrainingAssignment::with(['materials', 'training'])
            ->where('employee_id', $employeeId)
            ->where('status', '!=', 'completed')
            ->get();

        return view('portal-training.materials.index', compact('assignments'));
    }

    /**
     * Display specific material detail
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $material = TrainingMaterial::with(['category', 'progress' => function($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        }])->findOrFail($id);

        // Cek apakah user punya akses ke material ini
        $hasAccess = $this->checkMaterialAccess($employeeId, $id);
        if (!$hasAccess) {
            return redirect()->route('portal-training.index')
                ->with('error', 'Anda tidak memiliki akses ke materi ini.');
        }

        // Ambil assignment terkait
        $assignment = TrainingAssignment::where('employee_id', $employeeId)
            ->whereHas('materials', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->first();

        // Ambil progress material ini
        $progress = TrainingMaterialProgress::where('assignment_id', $assignment->id)
            ->where('material_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        // Jika belum ada progress, buat baru
        if (!$progress) {
            $progress = TrainingMaterialProgress::create([
                'assignment_id' => $assignment->id,
                'material_id' => $id,
                'employee_id' => $employeeId,
                'status' => 'not_started',
                'progress_percentage' => 0,
            ]);
        }

        // Ambil next material (urutan berikutnya)
        $nextMaterial = TrainingMaterial::where('category_id', $material->category_id)
            ->where('order', '>', $material->order)
            ->orderBy('order', 'asc')
            ->first();

        // Ambil previous material (urutan sebelumnya)
        $previousMaterial = TrainingMaterial::where('category_id', $material->category_id)
            ->where('order', '<', $material->order)
            ->orderBy('order', 'desc')
            ->first();

        return view('portal-training.materials.show', compact(
            'material',
            'progress',
            'assignment',
            'nextMaterial',
            'previousMaterial'
        ));
    }

    /**
     * Update material progress (tracking video watch time)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'current_time' => 'required|numeric',
            'duration' => 'required|numeric',
            'assignment_id' => 'required|exists:training_assignments,id',
        ]);

        $user = Auth::user();
        $employeeId = $user->id;

        $material = TrainingMaterial::findOrFail($id);

        // Hitung progress percentage
        $progressPercentage = ($request->current_time / $request->duration) * 100;

        // Update atau buat progress record
        $progress = TrainingMaterialProgress::updateOrCreate(
            [
                'assignment_id' => $request->assignment_id,
                'material_id' => $id,
                'employee_id' => $employeeId,
            ],
            [
                'status' => $progressPercentage >= 95 ? 'completed' : 'watching',
                'progress_percentage' => min($progressPercentage, 100),
                'watch_start_time' => TrainingMaterialProgress::where('assignment_id', $request->assignment_id)
                    ->where('material_id', $id)
                    ->where('employee_id', $employeeId)
                    ->first()
                    ? TrainingMaterialProgress::where('assignment_id', $request->assignment_id)
                        ->where('material_id', $id)
                        ->where('employee_id', $employeeId)
                        ->first()->watch_start_time
                    : Carbon::now(),
                'watch_end_time' => $progressPercentage >= 95 ? Carbon::now() : null,
                'watch_duration' => $request->current_time,
            ]
        );

        // Jika progress 100%, update status assignment
        if ($progressPercentage >= 95) {
            $this->updateAssignmentProgress($request->assignment_id);
        }

        return response()->json([
            'success' => true,
            'progress' => $progressPercentage,
            'status' => $progress->status,
            'message' => 'Progress berhasil diupdate'
        ]);
    }

    /**
     * Check if user has access to material
     *
     * @param int $employeeId
     * @param int $materialId
     * @return bool
     */
    private function checkMaterialAccess($employeeId, $materialId)
    {
        $assignment = TrainingAssignment::where('employee_id', $employeeId)
            ->whereHas('materials', function($query) use ($materialId) {
                $query->where('id', $materialId);
            })
            ->first();

        return $assignment !== null;
    }

    /**
     * Update assignment progress based on completed materials
     *
     * @param int $assignmentId
     * @return void
     */
    private function updateAssignmentProgress($assignmentId)
    {
        $assignment = TrainingAssignment::find($assignmentId);

        $totalMaterials = TrainingMaterialProgress::where('assignment_id', $assignmentId)->count();
        $completedMaterials = TrainingMaterialProgress::where('assignment_id', $assignmentId)
            ->where('status', 'completed')
            ->count();

        if ($totalMaterials > 0) {
            $progressPercentage = ($completedMaterials / $totalMaterials) * 100;
            $assignment->progress_percentage = $progressPercentage;

            // Jika semua materi selesai, update status assignment
            if ($progressPercentage >= 100) {
                $assignment->status = 'completed';
            }

            $assignment->save();
        }
    }
}
