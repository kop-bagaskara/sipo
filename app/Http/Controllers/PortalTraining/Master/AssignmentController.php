<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaster;
use App\Models\TrainingMaterial;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $trainings = TrainingMaster::where('is_active', true)->get();
        $employees = User::all();
        $allMaterials = TrainingMaterial::where('is_active', true)->get();
        return view('portal-training.master.assignments.index', compact('trainings', 'employees', 'allMaterials'));
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $assignments = TrainingAssignment::with(['training', 'employee', 'materials'])
            ->orderBy('assigned_date', 'desc');

        return DataTables::of($assignments)
            ->addIndexColumn()
            ->addColumn('training_name', function($assignment) {
                return $assignment->training ? $assignment->training->training_name : '-';
            })
            ->addColumn('employee_name', function($assignment) {
                return $assignment->employee ? $assignment->employee->name : '-';
            })
            ->addColumn('materials_count', function($assignment) {
                return $assignment->materials->count();
            })
            ->addColumn('status_badge', function($assignment) {
                $colors = [
                    'assigned' => 'info',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    'expired' => 'danger',
                ];
                $labels = [
                    'assigned' => 'Ditetapkan',
                    'in_progress' => 'Sedang Dikerjakan',
                    'completed' => 'Selesai',
                    'expired' => 'Expired',
                ];
                $color = $colors[$assignment->status] ?? 'secondary';
                $label = $labels[$assignment->status] ?? $assignment->status;
                return '<span class="badge badge-'.$color.'">'.$label.'</span>';
            })
            ->addColumn('progress_bar', function($assignment) {
                $progress = $assignment->progress_percentage ?? 0;
                $color = $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning');
                return '
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-'.$color.'" role="progressbar"
                             style="width: '.$progress.'%"
                             aria-valuenow="'.$progress.'"
                             aria-valuemin="0" aria-valuemax="100">
                            '.number_format($progress, 1).'%
                        </div>
                    </div>
                ';
            })
            ->addColumn('dates', function($assignment) {
                return '
                    <small>
                        <div>Assign: '.$assignment->assigned_date->format('d M Y').'</div>
                        <div>Deadline: '.$assignment->deadline_date->format('d M Y').'</div>
                    </small>
                ';
            })
            ->addColumn('action', function($assignment) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-primary btn-view" data-id="'.$assignment->id.'" title="Lihat">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$assignment->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$assignment->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'progress_bar', 'dates', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new assignment
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $trainings = TrainingMaster::where('is_active', true)->get();
        $employees = User::all();
        $materials = TrainingMaterial::where('is_active', true)->get();
        return view('portal-training.master.assignments.create', compact('trainings', 'employees', 'materials'));
    }

    /**
     * Store a new assignment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:training_masters,id',
            'employee_id' => 'required|exists:users,id',
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:training_materials,id',
            'deadline_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $assignment = TrainingAssignment::create([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'deadline_date' => $request->deadline_date,
            'status' => 'assigned',
            'progress_percentage' => 0,
            'assigned_date' => Carbon::now(),
            'notes' => $request->notes,
        ]);

        // Attach materials
        $assignment->materials()->attach($request->material_ids);

        return response()->json([
            'success' => true,
            'message' => 'Training assignment berhasil dibuat.',
            'data' => $assignment
        ]);
    }

    /**
     * Display the specified assignment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $assignment = TrainingAssignment::with([
            'training',
            'employee',
            'materials.category',
            'progress'
        ])->findOrFail($id);

        // Generate materials HTML
        $materialsHtml = '';
        if ($assignment->materials && $assignment->materials->count() > 0) {
            foreach ($assignment->materials as $material) {
                $materialsHtml .= '<span class="badge badge-info mr-1" style="margin: 2px;">' . $material->material_title . '</span> ';
            }
        }

        // Generate status badge
        $colors = [
            'assigned' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'expired' => 'danger',
        ];
        $labels = [
            'assigned' => 'Ditetapkan',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'expired' => 'Expired',
        ];
        $color = $colors[$assignment->status] ?? 'secondary';
        $label = $labels[$assignment->status] ?? $assignment->status;
        $statusBadge = '<span class="badge badge-' . $color . '">' . $label . '</span>';

        // Generate progress HTML
        $progress = $assignment->progress_percentage ?? 0;
        $progressColor = $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning');
        $progressHtml = '
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-' . $progressColor . '" role="progressbar"
                     style="width: ' . $progress . '%"
                     aria-valuenow="' . $progress . '"
                     aria-valuemin="0" aria-valuemax="100">
                    ' . number_format($progress, 1) . '%
                </div>
            </div>';

        return response()->json([
            'id' => $assignment->id,
            'training_name' => $assignment->training ? $assignment->training->training_name : '-',
            'employee_name' => $assignment->employee ? $assignment->employee->name : '-',
            'materials_html' => $materialsHtml ?: '-',
            'assigned_date' => $assignment->assigned_date->format('d M Y'),
            'deadline_date' => $assignment->deadline_date->format('d M Y'),
            'progress_html' => $progressHtml,
            'progress_percentage' => number_format($progress, 1),
            'status_badge' => $statusBadge,
            'status' => $assignment->status,
            'notes' => $assignment->notes ?? '-',
            'created_at' => $assignment->created_at->format('d M Y H:i'),
        ]);
    }

    /**
     * Show the form for editing assignment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $assignment = TrainingAssignment::with(['training', 'employee', 'materials'])->findOrFail($id);

        // Get material IDs array
        $materialIds = $assignment->materials->pluck('id')->toArray();

        return response()->json([
            'id' => $assignment->id,
            'training_id' => $assignment->training_id,
            'training_name' => $assignment->training ? $assignment->training->training_name : '-',
            'employee_id' => $assignment->employee_id,
            'employee_name' => $assignment->employee ? $assignment->employee->name : '-',
            'material_ids' => $materialIds,
            'deadline_date' => $assignment->deadline_date->format('Y-m-d'),
            'notes' => $assignment->notes ?? '',
            'status' => $assignment->status,
            'progress_percentage' => $assignment->progress_percentage ?? 0,
        ]);
    }

    /**
     * Update assignment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'training_id' => 'required|exists:training_masters,id',
            'employee_id' => 'required|exists:users,id',
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:training_materials,id',
            'deadline_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $assignment = TrainingAssignment::findOrFail($id);
        $assignment->update([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'deadline_date' => $request->deadline_date,
            'notes' => $request->notes,
        ]);

        // Sync materials
        $assignment->materials()->sync($request->material_ids);

        return response()->json([
            'success' => true,
            'message' => 'Training assignment berhasil diupdate.',
            'data' => $assignment
        ]);
    }

    /**
     * Delete assignment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $assignment = TrainingAssignment::findOrFail($id);

        // Detach materials
        $assignment->materials()->detach();

        // Delete related progress
        $assignment->progress()->delete();

        // Delete assignment
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training assignment berhasil dihapus.'
        ]);
    }

    /**
     * Bulk assign training to multiple employees
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:training_masters,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:training_materials,id',
            'deadline_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $assignments = [];
        foreach ($request->employee_ids as $employeeId) {
            $assignment = TrainingAssignment::create([
                'training_id' => $request->training_id,
                'employee_id' => $employeeId,
                'deadline_date' => $request->deadline_date,
                'status' => 'assigned',
                'progress_percentage' => 0,
                'assigned_date' => Carbon::now(),
                'notes' => $request->notes,
            ]);

            // Attach materials
            $assignment->materials()->attach($request->material_ids);

            $assignments[] = $assignment;
        }

        return response()->json([
            'success' => true,
            'message' => count($assignments) . ' training assignment berhasil dibuat.',
            'data' => $assignments
        ]);
    }

    /**
     * Get materials by training ID
     *
     * @param int $trainingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMaterialsByTraining($trainingId)
    {
        $training = TrainingMaster::with('materials')->findOrFail($trainingId);

        return response()->json([
            'success' => true,
            'materials' => $training->materials
        ]);
    }
}
