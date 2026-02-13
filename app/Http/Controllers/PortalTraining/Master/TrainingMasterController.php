<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingMaster;
use App\Models\TrainingMaterial;
use App\Models\TrainingMaterialCategory;
use App\Models\TrainingDifficultyLevel;
use App\Models\TrainingSession;
use App\Models\Divisi;
use App\Models\User;
use App\Services\GoogleDriveService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingMasterController extends Controller
{
    /**
     * Display a listing of training masters
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('portal-training.master.training-masters.index');
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $trainings = TrainingMaster::withCount(['materials', 'sessions'])->orderBy('created_at', 'desc');

        return DataTables::of($trainings)
            ->addIndexColumn()
            ->addColumn('training_name', function($training) {
                return $training->training_name;
            })
            ->addColumn('training_code', function($training) {
                return $training->training_code;
            })
            ->addColumn('training_type', function($training) {
                $types = [
                    'mandatory' => '<span class="badge badge-danger">Mandatory</span>',
                    'optional' => '<span class="badge badge-info">Optional</span>',
                    'certification' => '<span class="badge badge-warning">Certification</span>',
                    'skill_development' => '<span class="badge badge-success">Skill Development</span>',
                ];
                return $types[$training->training_type] ?? $training->training_type;
            })
            ->addColumn('training_method', function($training) {
                $methods = [
                    'classroom' => 'Kelas Tatap Muka',
                    'online' => 'Online',
                    'hybrid' => 'Hybrid',
                    'workshop' => 'Workshop',
                    'seminar' => 'Seminar',
                ];
                return $methods[$training->training_method] ?? $training->training_method;
            })
            ->addColumn('materials_count', function($training) {
                return $training->materials_count ?? 0;
            })
            ->addColumn('sessions_count', function($training) {
                return $training->sessions_count ?? 0;
            })
            ->addColumn('status_badge', function($training) {
                $statuses = [
                    'draft' => '<span class="badge badge-secondary">Draft</span>',
                    'published' => '<span class="badge badge-success">Published</span>',
                    'ongoing' => '<span class="badge badge-primary">Ongoing</span>',
                    'completed' => '<span class="badge badge-info">Completed</span>',
                    'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                ];
                return $statuses[$training->status] ?? $training->status;
            })
            ->addColumn('action', function($training) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-primary btn-view" data-id="'.$training->id.'" title="Lihat">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$training->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$training->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['training_type', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new training
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Divisi::all();
        $users = User::all();
        $materials = TrainingMaterial::with('category')->active()->ordered()->get();
        $categories = TrainingMaterialCategory::all();

        // Group materials by category
        $materialsByCategory = [];
        $materialsWithoutCategory = [];

        foreach ($materials as $material) {
            if ($material->category_id && $material->category) {
                $catId = $material->category_id;
                if (!isset($materialsByCategory[$catId])) {
                    $materialsByCategory[$catId] = [
                        'category' => $material->category,
                        'materials' => []
                    ];
                }
                $materialsByCategory[$catId]['materials'][] = $material;
            } else {
                $materialsWithoutCategory[] = $material;
            }
        }

        return view('portal-training.master.training-masters.create', compact('departments', 'users', 'materials', 'materialsByCategory', 'materialsWithoutCategory', 'categories'));
    }

    /**
     * Store a newly created training
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_name' => 'required|string|max:255',
            'training_type' => 'required|in:mandatory,optional,certification,skill_development',
            'training_method' => 'required|in:classroom,online,hybrid,workshop,seminar',
            'target_departments' => 'nullable|array',
            'notes' => 'nullable|string',
            'material_ids' => 'nullable|array',
            'material_ids.*' => 'exists:tb_training_materials,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::connection('pgsql3')->beginTransaction();
        try {
            $training = TrainingMaster::create([
                'training_name' => $request->training_name,
                'training_type' => $request->training_type,
                'training_method' => $request->training_method,
                'target_departments' => $request->target_departments,
                'notes' => $request->notes,
                'allow_retry' => $request->has('allow_retry') && $request->allow_retry == '1',
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            // Attach training materials
            if ($request->has('material_ids') && is_array($request->material_ids) && !empty($request->material_ids)) {
                $materialIds = array_filter($request->material_ids);

                // Validate all material IDs exist
                $validMaterialIds = [];
                foreach ($materialIds as $materialId) {
                    $material = TrainingMaterial::on('pgsql3')->find($materialId);
                    if ($material) {
                        $validMaterialIds[] = (int) $materialId;
                    }
                }

                // Attach materials dengan display_order
                if (!empty($validMaterialIds)) {
                    $pivotData = [];
                    foreach ($validMaterialIds as $order => $materialId) {
                        $pivotData[] = [
                            'training_id' => $training->id,
                            'material_id' => $materialId,
                            'display_order' => $order + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    DB::connection('pgsql3')->table('tb_training_master_material')->insert($pivotData);
                }
            }

            // Handle sessions
            if ($request->has('sessions') && is_array($request->sessions)) {
                foreach ($request->sessions as $order => $sessionData) {
                    if (empty($sessionData['session_title'])) {
                        continue; // Skip jika tidak ada title
                    }

                    // Handle video input - auto-detect Google Drive or local
                    $googleDriveFileId = null;
                    $videoUrl = null;
                    $videoSource = 'local';

                    // Check if using new unified input field
                    if (isset($sessionData['video_input']) && !empty($sessionData['video_input'])) {
                        $videoInput = trim($sessionData['video_input']);

                        // Check if it's Google Drive link
                        if (stripos($videoInput, 'drive.google.com') !== false ||
                            stripos($videoInput, 'docs.google.com') !== false ||
                            preg_match('/^[a-zA-Z0-9_-]{20,}$/', $videoInput)) {
                            // It's Google Drive
                            $googleDriveFileId = GoogleDriveService::extractFileId($videoInput);
                            $videoSource = 'google_drive';
                        } else {
                            // It's local path/URL
                            $videoUrl = $videoInput;
                            $videoSource = 'local';
                        }
                    } else {
                        // Fallback to old method (for backward compatibility)
                        $videoSource = $sessionData['video_source'] ?? 'local';

                        if ($videoSource === 'google_drive' && !empty($sessionData['google_drive_file_id'])) {
                            $googleDriveFileId = GoogleDriveService::extractFileId($sessionData['google_drive_file_id']);
                        } else {
                            $videoUrl = $sessionData['video_url'] ?? null;
                        }
                    }

                    TrainingSession::create([
                        'training_id' => $training->id,
                        'session_order' => (int) $sessionData['session_order'] ?? ($order + 1),
                        'session_title' => $sessionData['session_title'],
                        'description' => $sessionData['description'] ?? null,
                        'difficulty_level_id' => !empty($sessionData['difficulty_level_id']) ? (int) $sessionData['difficulty_level_id'] : null,
                        'theme' => $sessionData['theme'] ?? null,
                        'question_count' => !empty($sessionData['question_count']) ? (int) $sessionData['question_count'] : 5,
                        'passing_score' => !empty($sessionData['passing_score']) ? (float) $sessionData['passing_score'] : 70.00,
                        'has_video' => isset($sessionData['has_video']) && $sessionData['has_video'] == '1',
                        'video_url' => $videoUrl,
                        'google_drive_file_id' => $googleDriveFileId,
                        'video_source' => $videoSource,
                        'video_duration_seconds' => !empty($sessionData['video_duration_seconds']) ? (int) $sessionData['video_duration_seconds'] : null,
                        'is_active' => true,
                        'display_order' => (int) $sessionData['session_order'] ?? ($order + 1),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::connection('pgsql3')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Training berhasil dibuat.',
                'data' => $training
            ]);
        } catch (\Exception $e) {
            DB::connection('pgsql3')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single training
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $training = TrainingMaster::with('materials.category')->findOrFail($id);

        return response()->json([
            'id' => $training->id,
            'training_name' => $training->training_name,
            'training_code' => $training->training_code,
            'training_type' => $training->training_type,
            'training_method' => $training->training_method,
            'status' => $training->status,
            'target_departments' => $training->target_departments,
            'notes' => $training->notes,
            'materials' => $training->materials->map(function($material) {
                return [
                    'id' => $material->id,
                    'material_code' => $material->material_code,
                    'material_title' => $material->material_title,
                    'description' => $material->description,
                    'category' => $material->category ? $material->category->category_name : null,
                    'video_path' => $material->video_path,
                    'video_duration_seconds' => $material->video_duration_seconds,
                    'display_order' => $material->pivot->display_order ?? null,
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing a training
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $training = TrainingMaster::with(['materials.category', 'sessions' => function($query) {
            $query->orderBy('session_order', 'asc');
        }])->findOrFail($id);
        $departments = Divisi::all();
        $users = User::all();
        $materials = TrainingMaterial::with('category')->active()->ordered()->get();
        $categories = TrainingMaterialCategory::all();
        $difficultyLevels = TrainingDifficultyLevel::active()->ordered()->get();

        // Group materials by category
        $materialsByCategory = [];
        $materialsWithoutCategory = [];

        foreach ($materials as $material) {
            if ($material->category_id && $material->category) {
                $catId = $material->category_id;
                if (!isset($materialsByCategory[$catId])) {
                    $materialsByCategory[$catId] = [
                        'category' => $material->category,
                        'materials' => []
                    ];
                }
                $materialsByCategory[$catId]['materials'][] = $material;
            } else {
                $materialsWithoutCategory[] = $material;
            }
        }

        // Get selected material IDs
        $selectedMaterialIds = $training->materials->pluck('id')->toArray();

        // Jika request AJAX, return hanya form content untuk modal
        if (request()->ajax()) {
            return view('portal-training.master.training-masters.partials.edit-form', compact('training', 'departments', 'users', 'materials', 'materialsByCategory', 'materialsWithoutCategory', 'categories', 'selectedMaterialIds', 'difficultyLevels'));
        }

        return view('portal-training.master.training-masters.edit', compact('training', 'departments', 'users', 'materials', 'materialsByCategory', 'materialsWithoutCategory', 'categories', 'selectedMaterialIds', 'difficultyLevels'));
    }

    /**
     * Update the specified training
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'training_name' => 'required|string|max:255',
            'training_type' => 'required|in:mandatory,optional,certification,skill_development',
            'training_method' => 'required|in:classroom,online,hybrid,workshop,seminar',
            'status' => 'required|in:draft,published,ongoing,completed,cancelled',
            'target_departments' => 'nullable|array',
            'notes' => 'nullable|string',
            'material_ids' => 'nullable|array',
            'material_ids.*' => 'exists:tb_training_materials,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $training = TrainingMaster::findOrFail($id);

        DB::connection('pgsql3')->beginTransaction();
        try {
            $training->update([
                'training_name' => $request->training_name,
                'training_type' => $request->training_type,
                'training_method' => $request->training_method,
                'status' => $request->status,
                'target_departments' => $request->target_departments,
                'notes' => $request->notes,
                'allow_retry' => $request->has('allow_retry') && $request->allow_retry == '1',
                'updated_by' => Auth::id()
            ]);

            // Update training materials
            // Hapus semua materials yang ada
            DB::connection('pgsql3')->table('tb_training_master_material')
                ->where('training_id', $training->id)
                ->delete();

            // Attach materials baru
            if ($request->has('material_ids') && is_array($request->material_ids) && !empty($request->material_ids)) {
                $materialIds = array_filter($request->material_ids);

                // Validate all material IDs exist
                $validMaterialIds = [];
                foreach ($materialIds as $materialId) {
                    $material = TrainingMaterial::on('pgsql3')->find($materialId);
                    if ($material) {
                        $validMaterialIds[] = (int) $materialId;
                    }
                }

                // Attach materials dengan display_order
                if (!empty($validMaterialIds)) {
                    $pivotData = [];
                    foreach ($validMaterialIds as $order => $materialId) {
                        $pivotData[] = [
                            'training_id' => $training->id,
                            'material_id' => $materialId,
                            'display_order' => $order + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    DB::connection('pgsql3')->table('tb_training_master_material')->insert($pivotData);
                }
            }

            // Handle sessions - Update existing, create new, delete removed
            if ($request->has('sessions') && is_array($request->sessions) && !empty($request->sessions)) {
                // Get existing session IDs
                $existingSessionIds = $training->sessions->pluck('id')->toArray();
                $submittedSessionIds = [];

                // Sort sessions by session_order untuk memastikan urutan benar
                $sessions = $request->sessions;
                uasort($sessions, function($a, $b) {
                    $orderA = (int) ($a['session_order'] ?? 0);
                    $orderB = (int) ($b['session_order'] ?? 0);
                    return $orderA <=> $orderB;
                });

                foreach ($sessions as $key => $sessionData) {
                    if (empty($sessionData['session_title'])) {
                        continue; // Skip jika tidak ada title
                    }

                    $sessionOrder = !empty($sessionData['session_order']) ? (int) $sessionData['session_order'] : (int) $key;

                    // Jika ada session ID, berarti update existing
                    if (!empty($sessionData['id'])) {
                        $sessionId = (int) $sessionData['id'];
                        $submittedSessionIds[] = $sessionId;

                        $session = TrainingSession::find($sessionId);
                        if ($session && $session->training_id == $training->id) {
                            // Handle video input - auto-detect Google Drive or local
                            $googleDriveFileId = null;
                            $videoUrl = null;
                            $videoSource = 'local';

                            // Check if using new unified input field
                            if (isset($sessionData['video_input']) && !empty($sessionData['video_input'])) {
                                $videoInput = trim($sessionData['video_input']);

                                // Check if it's Google Drive link
                                if (stripos($videoInput, 'drive.google.com') !== false ||
                                    stripos($videoInput, 'docs.google.com') !== false ||
                                    preg_match('/^[a-zA-Z0-9_-]{20,}$/', $videoInput)) {
                                    // It's Google Drive
                                    $googleDriveFileId = GoogleDriveService::extractFileId($videoInput);
                                    $videoSource = 'google_drive';
                                } else {
                                    // It's local path/URL
                                    $videoUrl = $videoInput;
                                    $videoSource = 'local';
                                }
                            } else {
                                // Fallback to old method (for backward compatibility)
                                $videoSource = $sessionData['video_source'] ?? 'local';

                                if ($videoSource === 'google_drive' && !empty($sessionData['google_drive_file_id'])) {
                                    $googleDriveFileId = GoogleDriveService::extractFileId($sessionData['google_drive_file_id']);
                                } else {
                                    $videoUrl = $sessionData['video_url'] ?? null;
                                }
                            }

                            $session->update([
                                'session_order' => $sessionOrder,
                                'session_title' => $sessionData['session_title'],
                                'description' => $sessionData['description'] ?? null,
                                'difficulty_level_id' => !empty($sessionData['difficulty_level_id']) ? (int) $sessionData['difficulty_level_id'] : null,
                                'theme' => $sessionData['theme'] ?? null,
                                'question_count' => !empty($sessionData['question_count']) ? (int) $sessionData['question_count'] : 5,
                                'passing_score' => !empty($sessionData['passing_score']) ? (float) $sessionData['passing_score'] : 70.00,
                                'has_video' => isset($sessionData['has_video']) && $sessionData['has_video'] == '1',
                                'video_url' => $videoUrl,
                                'google_drive_file_id' => $googleDriveFileId,
                                'video_source' => $videoSource,
                                'video_duration_seconds' => !empty($sessionData['video_duration_seconds']) ? (int) $sessionData['video_duration_seconds'] : null,
                                'display_order' => $sessionOrder,
                                'updated_by' => Auth::id(),
                            ]);
                        }
                    } else {
                        // Create new session
                        // Handle video input - auto-detect Google Drive or local
                        $googleDriveFileId = null;
                        $videoUrl = null;
                        $videoSource = 'local';

                        // Check if using new unified input field
                        if (isset($sessionData['video_input']) && !empty($sessionData['video_input'])) {
                            $videoInput = trim($sessionData['video_input']);

                            // Check if it's Google Drive link
                            if (stripos($videoInput, 'drive.google.com') !== false ||
                                stripos($videoInput, 'docs.google.com') !== false ||
                                preg_match('/^[a-zA-Z0-9_-]{20,}$/', $videoInput)) {
                                // It's Google Drive
                                $googleDriveFileId = GoogleDriveService::extractFileId($videoInput);
                                $videoSource = 'google_drive';
                            } else {
                                // It's local path/URL
                                $videoUrl = $videoInput;
                                $videoSource = 'local';
                            }
                        } else {
                            // Fallback to old method (for backward compatibility)
                            $videoSource = $sessionData['video_source'] ?? 'local';

                            if ($videoSource === 'google_drive' && !empty($sessionData['google_drive_file_id'])) {
                                $googleDriveFileId = GoogleDriveService::extractFileId($sessionData['google_drive_file_id']);
                            } else {
                                $videoUrl = $sessionData['video_url'] ?? null;
                            }
                        }

                        TrainingSession::create([
                            'training_id' => $training->id,
                            'session_order' => $sessionOrder,
                            'session_title' => $sessionData['session_title'],
                            'description' => $sessionData['description'] ?? null,
                            'difficulty_level_id' => !empty($sessionData['difficulty_level_id']) ? (int) $sessionData['difficulty_level_id'] : null,
                            'theme' => $sessionData['theme'] ?? null,
                            'question_count' => !empty($sessionData['question_count']) ? (int) $sessionData['question_count'] : 5,
                            'passing_score' => !empty($sessionData['passing_score']) ? (float) $sessionData['passing_score'] : 70.00,
                            'has_video' => isset($sessionData['has_video']) && $sessionData['has_video'] == '1',
                            'video_url' => $videoUrl,
                            'google_drive_file_id' => $googleDriveFileId,
                            'video_source' => $videoSource,
                            'video_duration_seconds' => !empty($sessionData['video_duration_seconds']) ? (int) $sessionData['video_duration_seconds'] : null,
                            'is_active' => true,
                            'display_order' => $sessionOrder,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }

                // Delete sessions yang tidak ada di request (dihapus dari form)
                $sessionsToDelete = array_diff($existingSessionIds, $submittedSessionIds);
                if (!empty($sessionsToDelete)) {
                    TrainingSession::whereIn('id', $sessionsToDelete)
                        ->where('training_id', $training->id)
                        ->delete();
                }
            }

            DB::connection('pgsql3')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Training berhasil diperbarui.',
                'data' => $training
            ]);
        } catch (\Exception $e) {
            DB::connection('pgsql3')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified training
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $training = TrainingMaster::findOrFail($id);

        // Delete related materials (pivot table akan terhapus otomatis karena cascade)
        DB::connection('pgsql3')->table('tb_training_master_material')
            ->where('training_id', $training->id)
            ->delete();

        $training->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training berhasil dihapus.'
        ]);
    }

    /**
     * Test Google Drive API connection
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function testGoogleDriveApi(Request $request)
    {
        $testFileId = $request->input('file_id');

        $googleDriveService = new GoogleDriveService();
        $result = $googleDriveService->testConnection($testFileId);

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        // Otherwise return view
        return view('portal-training.master.test-google-drive', compact('result', 'testFileId'));
    }
}

