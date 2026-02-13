<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaster;
use App\Models\TrainingMaterial;
use App\Models\User;
use App\Models\Divisi;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        
        // Get employees grouped by divisi
        $employeesByDivisi = User::with('divisiUser')
            ->whereNotNull('divisi')
            ->orderBy('divisi')
            ->orderBy('name')
            ->get()
            ->groupBy('divisi');
        
        // Get all divisi for grouping
        $divisis = Divisi::orderBy('divisi')->get();
        
        $allMaterials = TrainingMaterial::with('category')->where('is_active', true)->get();
        
        // Group materials by category
        $materialsByCategory = [];
        $materialsWithoutCategory = [];
        
        foreach ($allMaterials as $material) {
            if ($material->category) {
                $categoryId = $material->category->id;
                if (!isset($materialsByCategory[$categoryId])) {
                    $materialsByCategory[$categoryId] = [
                        'category' => $material->category,
                        'materials' => []
                    ];
                }
                $materialsByCategory[$categoryId]['materials'][] = $material;
            } else {
                $materialsWithoutCategory[] = $material;
            }
        }
        
        // Get all categories
        $categories = collect($materialsByCategory)->pluck('category')->unique('id')->values();
        
        return view('portal-training.master.assignments.index', compact('trainings', 'employeesByDivisi', 'divisis', 'allMaterials', 'materialsByCategory', 'materialsWithoutCategory', 'categories'));
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        // Query assignments dengan group by session_code
        // Untuk session_code yang sama, hanya ambil yang pertama (MIN id)
        $assignments = TrainingAssignment::with(['training', 'employee'])
            ->withCount('materials')
            ->whereRaw('
                (session_code IS NULL) OR 
                (session_code IS NOT NULL AND id = (
                    SELECT MIN(id) 
                    FROM tb_training_assignments ta2 
                    WHERE ta2.session_code = tb_training_assignments.session_code
                    AND ta2.deleted_at IS NULL
                ))
            ')
            ->orderBy('assigned_date', 'desc');

        return DataTables::of($assignments)
            ->addIndexColumn()
            ->addColumn('training_name', function($assignment) {
                return $assignment->training ? $assignment->training->training_name : '-';
            })
            ->addColumn('employee_name', function($assignment) {
                // Jika ada session_code, tampilkan jumlah karyawan, bukan nama individual
                if ($assignment->session_code) {
                    $count = TrainingAssignment::where('session_code', $assignment->session_code)->count();
                    return '<span class="badge badge-primary">' . $count . ' Karyawan</span>';
                }
                return $assignment->employee ? $assignment->employee->name : '-';
            })
            ->addColumn('materials_count', function($assignment) {
                // Jika ada session_code, ambil materials_count dari representatif (sama untuk semua dalam sesi)
                return $assignment->materials_count ?? 0;
            })
            ->addColumn('session_code', function($assignment) {
                if ($assignment->session_code) {
                    return '<span class="badge badge-info">' . $assignment->session_code . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_badge', function($assignment) {
                // Jika ada session_code, hitung status agregat
                if ($assignment->session_code) {
                    $sessionAssignments = TrainingAssignment::where('session_code', $assignment->session_code)->get();
                    $statusCounts = $sessionAssignments->groupBy('status')->map->count();
                    
                    // Tampilkan status yang paling banyak, atau "Mixed" jika ada beberapa status
                    if ($statusCounts->count() > 1) {
                        return '<span class="badge badge-warning">Campuran</span>';
                    } else {
                        $status = $statusCounts->keys()->first();
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
                        $color = $colors[$status] ?? 'secondary';
                        $label = $labels[$status] ?? $status;
                        return '<span class="badge badge-'.$color.'">'.$label.'</span>';
                    }
                } else {
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
                }
            })
            ->addColumn('progress_bar', function($assignment) {
                // Jika ada session_code, hitung progress rata-rata
                if ($assignment->session_code) {
                    $avgProgress = TrainingAssignment::where('session_code', $assignment->session_code)
                        ->avg('progress_percentage') ?? 0;
                    $progress = round($avgProgress, 1);
                } else {
                    $progress = $assignment->progress_percentage ?? 0;
                }
                
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
                // Jika ada session_code, ambil tanggal dari representatif (sama untuk semua dalam sesi)
                $startDateText = $assignment->start_date ? $assignment->start_date->format('d M Y') : '-';
                return '
                    <small>
                        <div>Assign: '.$assignment->assigned_date->format('d M Y').'</div>
                        <div>Mulai: '.$startDateText.'</div>
                        <div>Deadline: '.$assignment->deadline_date->format('d M Y').'</div>
                    </small>
                ';
            })
            ->addColumn('action', function($assignment) {
                $today = Carbon::now()->toDateString();
                $startDate = $assignment->start_date ? $assignment->start_date->format('Y-m-d') : null;
                $canStart = $startDate && $startDate <= $today && !$assignment->is_opened;
                
                // Jika ada session_code, action untuk view semua assignment dalam sesi (TIDAK BISA EDIT)
                if ($assignment->session_code) {
                    $canStartSession = $startDate && $startDate <= $today;
                    $sessionAssignments = TrainingAssignment::where('session_code', $assignment->session_code)->get();
                    // Cek apakah semua assignment belum dibuka (is_opened = false)
                    $allNotOpened = $sessionAssignments->every(function($a) {
                        return !$a->is_opened;
                    });
                    $canStartSession = $canStartSession && $allNotOpened;
                    
                    $startButton = '';
                    if ($canStartSession) {
                        $startButton = '<button type="button" class="btn btn-sm btn-success btn-start-session" data-session-code="'.$assignment->session_code.'" title="Mulai Training Sesi">
                            <i class="mdi mdi-play"></i> Start
                        </button>';
                    } else if ($startDate && $startDate > $today) {
                        $startButton = '<button type="button" class="btn btn-sm btn-secondary" disabled title="Training mulai: '.$assignment->start_date->format('d M Y').'">
                            <i class="mdi mdi-clock-outline"></i> Start
                        </button>';
                    }
                    
                    return '
                        <div class="action-buttons text-center">
                            <button type="button" class="btn btn-sm btn-primary btn-view-session" data-session-code="'.$assignment->session_code.'" title="Lihat Detail Sesi">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            '.$startButton.'
                            <button type="button" class="btn btn-sm btn-danger btn-delete-session" data-session-code="'.$assignment->session_code.'" title="Hapus Sesi">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                } else {
                    // Single assign: bisa edit, bisa start
                    $startButton = '';
                    if ($canStart) {
                        $startButton = '<button type="button" class="btn btn-sm btn-success btn-start" data-id="'.$assignment->id.'" title="Mulai Training">
                            <i class="mdi mdi-play"></i> Start
                        </button>';
                    } else if ($startDate && $startDate > $today) {
                        $startButton = '<button type="button" class="btn btn-sm btn-secondary" disabled title="Training mulai: '.$assignment->start_date->format('d M Y').'">
                            <i class="mdi mdi-clock-outline"></i> Start
                        </button>';
                    }
                    
                    return '
                        <div class="action-buttons text-center">
                            <button type="button" class="btn btn-sm btn-primary btn-view" data-id="'.$assignment->id.'" title="Lihat">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$assignment->id.'" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            '.$startButton.'
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$assignment->id.'" title="Hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                }
            })
            ->rawColumns(['status_badge', 'progress_bar', 'dates', 'session_code', 'employee_name', 'action'])
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
        
        // Get employees grouped by divisi
        $employeesByDivisi = User::with('divisiUser')
            ->whereNotNull('divisi')
            ->orderBy('divisi')
            ->orderBy('name')
            ->get()
            ->groupBy('divisi');
        
        // Get all divisi for grouping
        $divisis = Divisi::orderBy('divisi')->get();
        
        $materials = TrainingMaterial::where('is_active', true)->get();
        return view('portal-training.master.assignments.create', compact('trainings', 'employeesByDivisi', 'divisis', 'materials'));
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
            'training_id' => 'required',
            'employee_id' => 'required',
            'material_ids' => 'required|array',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        // Validasi training_id dengan connection yang benar (pgsql2)
        $training = TrainingMaster::on('pgsql2')->find($request->training_id);
        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training ID tidak valid: ' . $request->training_id . '. Tabel: tb_training_masters (pgsql2).'
            ], 400);
        }

        // Validasi employee_id dengan connection yang benar (pgsql)
        $employee = User::on('pgsql')->find($request->employee_id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID tidak valid: ' . $request->employee_id . '. Tabel: users (pgsql).'
            ], 400);
        }

        // Validasi material_ids dengan connection yang benar (pgsql3)
        foreach ($request->material_ids as $materialId) {
            $material = TrainingMaterial::on('pgsql3')->find($materialId);
            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID tidak valid: ' . $materialId . '. Tabel: tb_training_materials (pgsql3).'
                ], 400);
            }
        }

        // Pastikan material_ids adalah array of integers untuk JSON column
        $materialIdsForJson = [];
        if (!empty($request->material_ids) && is_array($request->material_ids)) {
            $materialIdsForJson = array_map('intval', $request->material_ids);
            $materialIdsForJson = array_filter($materialIdsForJson);
            $materialIdsForJson = array_values($materialIdsForJson);
        }
        
        $assignment = TrainingAssignment::create([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'material_ids' => !empty($materialIdsForJson) ? $materialIdsForJson : null, // Isi JSON column
            'start_date' => $request->start_date ?? $request->assigned_date ?? Carbon::now()->toDateString(),
            'deadline_date' => $request->deadline_date,
            'status' => 'assigned',
            'progress_percentage' => 0,
            'assigned_date' => Carbon::now(),
            'notes' => $request->notes,
        ]);

        // Attach materials dengan connection yang benar
        if (!empty($request->material_ids) && is_array($request->material_ids)) {
            try {
                // Pastikan material_ids adalah array of integers
                $materialIds = array_map('intval', $request->material_ids);
                $materialIds = array_filter($materialIds); // Remove empty values
                
                if (empty($materialIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Material IDs tidak valid setelah konversi.'
                    ], 400);
                }
                
                // Validasi material IDs exist
                foreach ($materialIds as $materialId) {
                    $material = TrainingMaterial::on('pgsql3')->find($materialId);
                    if (!$material) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Material ID ' . $materialId . ' tidak ditemukan di database.'
                        ], 400);
                    }
                }
                
                // Manual insert ke pivot table untuk memastikan connection benar
                $pivotTable = 'tb_training_assignment_material';
                foreach ($materialIds as $order => $materialId) {
                    DB::connection('pgsql3')->table($pivotTable)->insertOrIgnore([
                        'assignment_id' => $assignment->id,
                        'material_id' => $materialId,
                        'order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Validasi: pastikan materials ter-attach dengan benar
                $assignment->refresh();
                $assignment->load('materials');
                $attachedCount = $assignment->materials->count();
                
                if ($attachedCount === 0) {
                    Log::error("Store Assignment: Materials tidak ter-attach untuk assignment ID {$assignment->id}", [
                        'assignment_id' => $assignment->id,
                        'material_ids' => $materialIds
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Assignment berhasil dibuat, namun gagal attach materials. Silakan edit assignment untuk menambahkan materials.',
                        'data' => $assignment
                    ], 400);
                } elseif ($attachedCount < count($materialIds)) {
                    Log::warning("Store Assignment: Tidak semua materials ter-attach untuk assignment ID {$assignment->id}", [
                        'assignment_id' => $assignment->id,
                        'expected_count' => count($materialIds),
                        'actual_count' => $attachedCount
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Store Assignment: Error attach materials", [
                    'assignment_id' => $assignment->id,
                    'material_ids' => $request->material_ids,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment berhasil dibuat, namun error saat attach materials: ' . $e->getMessage(),
                    'data' => $assignment
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Material IDs tidak boleh kosong.'
            ], 400);
        }

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
            'training.sessions',
            'employee',
            'materials.category',
            'progress',
            'sessionProgress.session'
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

        // Generate session progress HTML
        $sessionsHtml = '';
        if ($assignment->training && $assignment->training->sessions) {
            $sessions = $assignment->training->sessions->sortBy('session_order');
            $sessionProgressMap = $assignment->sessionProgress->keyBy('session_id');
            
            $sessionsHtml = '<div class="table-responsive mt-3">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Sesi</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Benar</th>
                            <th>Total Soal</th>
                            <th>Tanggal Selesai</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($sessions as $session) {
                $sessionProgress = $sessionProgressMap->get($session->id);
                
                if ($sessionProgress) {
                    $statusColors = [
                        'not_started' => 'secondary',
                        'in_progress' => 'warning',
                        'completed' => 'info',
                        'passed' => 'success',
                        'failed' => 'danger',
                    ];
                    $statusLabels = [
                        'not_started' => 'Belum Dimulai',
                        'in_progress' => 'Sedang Dikerjakan',
                        'completed' => 'Selesai',
                        'passed' => 'Lulus',
                        'failed' => 'Tidak Lulus',
                    ];
                    $statusColor = $statusColors[$sessionProgress->status] ?? 'secondary';
                    $statusLabel = $statusLabels[$sessionProgress->status] ?? $sessionProgress->status;
                    $statusBadgeHtml = '<span class="badge badge-' . $statusColor . '">' . $statusLabel . '</span>';
                    
                    $score = $sessionProgress->score ?? 0;
                    $correctAnswers = $sessionProgress->correct_answers_count ?? 0;
                    $totalQuestions = $sessionProgress->total_questions ?? 0;
                    $completedAt = $sessionProgress->completed_at ? $sessionProgress->completed_at->format('d M Y H:i') : '-';
                    
                    $sessionsHtml .= '<tr>
                        <td><strong>Sesi ' . $session->session_order . '</strong></td>
                        <td>' . $session->session_title . '</td>
                        <td>' . $statusBadgeHtml . '</td>
                        <td><strong>' . number_format($score, 2) . '</strong></td>
                        <td>' . $correctAnswers . '</td>
                        <td>' . $totalQuestions . '</td>
                        <td>' . $completedAt . '</td>
                    </tr>';
                } else {
                    $sessionsHtml .= '<tr>
                        <td><strong>Sesi ' . $session->session_order . '</strong></td>
                        <td>' . $session->session_title . '</td>
                        <td><span class="badge badge-secondary">Belum Dimulai</span></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>';
                }
            }
            
            $sessionsHtml .= '</tbody></table></div>';
        } else {
            $sessionsHtml = '<p class="text-muted">Tidak ada sesi training.</p>';
        }

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
            'sessions_html' => $sessionsHtml,
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
            'start_date' => $assignment->start_date ? $assignment->start_date->format('Y-m-d') : '',
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
            'training_id' => 'required',
            'employee_id' => 'required',
            'material_ids' => 'required|array',
            'deadline_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Validasi training_id dengan connection yang benar (pgsql2)
        $training = TrainingMaster::on('pgsql2')->find($request->training_id);
        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training ID tidak valid: ' . $request->training_id . '. Tabel: tb_training_masters (pgsql2).'
            ], 400);
        }

        // Validasi employee_id dengan connection yang benar (pgsql)
        $employee = User::on('pgsql')->find($request->employee_id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID tidak valid: ' . $request->employee_id . '. Tabel: users (pgsql).'
            ], 400);
        }

        // Validasi material_ids dengan connection yang benar (pgsql3)
        foreach ($request->material_ids as $materialId) {
            $material = TrainingMaterial::on('pgsql3')->find($materialId);
            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID tidak valid: ' . $materialId . '. Tabel: tb_training_materials (pgsql3).'
                ], 400);
            }
        }

        $assignment = TrainingAssignment::findOrFail($id);
        $request->validate([
            'training_id' => 'required',
            'employee_id' => 'required',
            'material_ids' => 'required|array',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        // Pastikan material_ids adalah array of integers untuk JSON column
        $materialIdsForJson = [];
        if (!empty($request->material_ids) && is_array($request->material_ids)) {
            $materialIdsForJson = array_map('intval', $request->material_ids);
            $materialIdsForJson = array_filter($materialIdsForJson);
            $materialIdsForJson = array_values($materialIdsForJson);
        }
        
        $assignment->update([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'material_ids' => !empty($materialIdsForJson) ? $materialIdsForJson : null, // Isi JSON column
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'notes' => $request->notes,
        ]);

        // Sync materials ke pivot table
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
        // Debug: Log request data
        Log::info('Bulk Assign Request', [
            'material_ids' => $request->material_ids,
            'material_ids_type' => gettype($request->material_ids),
            'employee_ids' => $request->employee_ids,
            'all_request' => $request->all()
        ]);
        
        $request->validate([
            'training_id' => 'required',
            'employee_ids' => 'required|array',
            'material_ids' => 'required|array',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);
        
        // Pastikan material_ids adalah array
        if (!is_array($request->material_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Material IDs harus berupa array. Diterima: ' . gettype($request->material_ids) . ' - ' . json_encode($request->material_ids)
            ], 400);
        }

        // Validasi training_id dengan connection yang benar (pgsql2)
        $training = TrainingMaster::on('pgsql2')->find($request->training_id);
        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training ID tidak valid: ' . $request->training_id . '. Tabel: tb_training_masters (pgsql2).'
            ], 400);
        }

        // Validasi employee_ids dengan connection yang benar (pgsql)
        foreach ($request->employee_ids as $employeeId) {
            $employee = User::on('pgsql')->find($employeeId);
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee ID tidak valid: ' . $employeeId . '. Tabel: users (pgsql).'
                ], 400);
            }
        }

        // Validasi material_ids dengan connection yang benar (pgsql3)
        foreach ($request->material_ids as $materialId) {
            $material = TrainingMaterial::on('pgsql3')->find($materialId);
            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID tidak valid: ' . $materialId . '. Tabel: tb_training_materials (pgsql3).'
                ], 400);
            }
        }

        // Generate kode unik untuk sesi bulk assign ini
        $sessionCode = $this->generateSessionCode($request->training_id);

        $assignments = [];
        $errors = [];
        
        foreach ($request->employee_ids as $employeeId) {
            try {
                // Pastikan material_ids adalah array of integers untuk JSON column
                $materialIdsForJson = [];
                if (!empty($request->material_ids) && is_array($request->material_ids)) {
                    $materialIdsForJson = array_map('intval', $request->material_ids);
                    $materialIdsForJson = array_filter($materialIdsForJson);
                    $materialIdsForJson = array_values($materialIdsForJson);
                }
                
                $assignment = TrainingAssignment::create([
                    'training_id' => $request->training_id,
                    'employee_id' => $employeeId,
                    'session_code' => $sessionCode, // Kode unik yang sama untuk semua dalam sesi ini
                    'material_ids' => !empty($materialIdsForJson) ? $materialIdsForJson : null, // Isi JSON column
                    'start_date' => $request->start_date ?? $request->assigned_date ?? Carbon::now()->toDateString(),
                    'deadline_date' => $request->deadline_date,
                    'status' => 'assigned',
                    'progress_percentage' => 0,
                    'assigned_date' => Carbon::now(),
                    'notes' => $request->notes,
                ]);

                // Attach materials dengan connection yang benar
                if (!empty($request->material_ids) && is_array($request->material_ids)) {
                    try {
                        // Pastikan material_ids adalah array of integers
                        $materialIds = array_map('intval', $request->material_ids);
                        $materialIds = array_filter($materialIds); // Remove empty values
                        $materialIds = array_values($materialIds); // Re-index array
                        
                        if (empty($materialIds)) {
                            $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Material IDs tidak valid setelah konversi.";
                            Log::error("Bulk Assign: Material IDs kosong setelah konversi", [
                                'assignment_id' => $assignment->id,
                                'employee_id' => $employeeId,
                                'original_material_ids' => $request->material_ids
                            ]);
                        } else {
                            // Validasi material IDs exist dan filter hanya yang valid
                            $validMaterialIds = [];
                            foreach ($materialIds as $materialId) {
                                $material = TrainingMaterial::on('pgsql3')->find($materialId);
                                if ($material) {
                                    $validMaterialIds[] = $materialId;
                                } else {
                                    $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Material ID {$materialId} tidak ditemukan di database.";
                                    Log::warning("Bulk Assign: Material ID tidak ditemukan", [
                                        'assignment_id' => $assignment->id,
                                        'employee_id' => $employeeId,
                                        'material_id' => $materialId
                                    ]);
                                }
                            }
                            
                            if (empty($validMaterialIds)) {
                                $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Tidak ada material yang valid untuk di-attach.";
                                Log::error("Bulk Assign: Tidak ada material valid", [
                                    'assignment_id' => $assignment->id,
                                    'employee_id' => $employeeId,
                                    'material_ids' => $materialIds
                                ]);
                            } else {
                                // Attach materials ke pivot table menggunakan connection yang benar
                                $pivotTable = 'tb_training_assignment_material';
                                
                                foreach ($validMaterialIds as $order => $materialId) {
                                    try {
                                        // Cek apakah sudah ada di pivot table
                                        $exists = DB::connection('pgsql3')->table($pivotTable)
                                            ->where('assignment_id', $assignment->id)
                                            ->where('material_id', $materialId)
                                            ->exists();
                                        
                                        if (!$exists) {
                                            // Pastikan assignment_id dan material_id valid
                                            $assignmentExists = DB::connection('pgsql3')->table('tb_training_assignments')
                                                ->where('id', $assignment->id)
                                                ->exists();
                                            
                                            $materialExists = DB::connection('pgsql3')->table('tb_training_materials')
                                                ->where('id', $materialId)
                                                ->exists();
                                            
                                            if (!$assignmentExists) {
                                                throw new \Exception("Assignment ID {$assignment->id} tidak ditemukan di tb_training_assignments");
                                            }
                                            
                                            if (!$materialExists) {
                                                throw new \Exception("Material ID {$materialId} tidak ditemukan di tb_training_materials");
                                            }
                                            
                                            DB::connection('pgsql3')->table($pivotTable)->insert([
                                                'assignment_id' => $assignment->id,
                                                'material_id' => $materialId,
                                                'order' => $order,
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ]);
                                            
                                            Log::info("Bulk Assign: Material berhasil di-insert ke pivot", [
                                                'assignment_id' => $assignment->id,
                                                'material_id' => $materialId,
                                                'order' => $order
                                            ]);
                                        }
                                    } catch (\Exception $insertError) {
                                        Log::error("Bulk Assign: Error insert material ke pivot", [
                                            'assignment_id' => $assignment->id,
                                            'material_id' => $materialId,
                                            'error' => $insertError->getMessage(),
                                            'sql_state' => $insertError->getCode(),
                                            'trace' => $insertError->getTraceAsString()
                                        ]);
                                        $errors[] = "Assignment ID {$assignment->id}: Error insert material ID {$materialId} - " . $insertError->getMessage();
                                    }
                                }
                                
                                // Validasi: pastikan materials ter-attach dengan benar
                                // Cek langsung di pivot table untuk memastikan data ter-insert
                                $pivotCount = DB::connection('pgsql3')->table($pivotTable)
                                    ->where('assignment_id', $assignment->id)
                                    ->count();
                                
                                // Clear relationship cache dan reload dengan cara yang lebih eksplisit
                                $assignment->unsetRelation('materials');
                                
                                // Coba load materials dengan cara manual untuk debugging
                                $materialIdsFromPivot = DB::connection('pgsql3')
                                    ->table($pivotTable)
                                    ->where('assignment_id', $assignment->id)
                                    ->pluck('material_id')
                                    ->toArray();
                                
                                Log::info("Bulk Assign: Material IDs dari pivot table", [
                                    'assignment_id' => $assignment->id,
                                    'material_ids_from_pivot' => $materialIdsFromPivot,
                                    'pivot_count' => $pivotCount
                                ]);
                                
                                // Reload assignment dengan materials menggunakan connection yang benar
                                $assignment = TrainingAssignment::on('pgsql3')
                                    ->with('materials')
                                    ->find($assignment->id);
                                
                                $attachedCount = $assignment->materials->count();
                                
                                Log::info("Bulk Assign: Materials setelah reload", [
                                    'assignment_id' => $assignment->id,
                                    'attached_count' => $attachedCount,
                                    'materials_collection' => $assignment->materials->pluck('id')->toArray()
                                ]);
                                
                                if ($attachedCount === 0 && $pivotCount > 0) {
                                    // Data ada di pivot table tapi relationship tidak bisa load
                                    // Ini kemungkinan masalah dengan relationship atau connection
                                    $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Materials ter-insert ke pivot table (count: {$pivotCount}) tapi relationship tidak bisa load (count: {$attachedCount}). Material IDs: " . implode(', ', $validMaterialIds);
                                    Log::error("Bulk Assign: Materials ada di pivot tapi relationship tidak load untuk assignment ID {$assignment->id}", [
                                        'assignment_id' => $assignment->id,
                                        'employee_id' => $employeeId,
                                        'material_ids' => $validMaterialIds,
                                        'pivot_count' => $pivotCount,
                                        'attached_count' => $attachedCount,
                                        'pivot_table' => $pivotTable,
                                        'connection' => 'pgsql3',
                                        'assignment_connection' => $assignment->getConnectionName(),
                                        'material_connection' => TrainingMaterial::make()->getConnectionName()
                                    ]);
                                } elseif ($attachedCount === 0 && $pivotCount === 0) {
                                    // Data tidak ter-insert sama sekali
                                    $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Gagal insert materials ke pivot table. Material IDs: " . implode(', ', $validMaterialIds);
                                    Log::error("Bulk Assign: Materials tidak ter-insert ke pivot table untuk assignment ID {$assignment->id}", [
                                        'assignment_id' => $assignment->id,
                                        'employee_id' => $employeeId,
                                        'material_ids' => $validMaterialIds,
                                        'pivot_table' => $pivotTable,
                                        'connection' => 'pgsql3'
                                    ]);
                                } elseif ($attachedCount < count($validMaterialIds)) {
                                    $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Hanya {$attachedCount} dari " . count($validMaterialIds) . " materials yang ter-attach.";
                                    Log::warning("Bulk Assign: Tidak semua materials ter-attach untuk assignment ID {$assignment->id}", [
                                        'assignment_id' => $assignment->id,
                                        'employee_id' => $employeeId,
                                        'expected_count' => count($validMaterialIds),
                                        'actual_count' => $attachedCount,
                                        'material_ids' => $validMaterialIds
                                    ]);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Error saat attach materials - " . $e->getMessage();
                        Log::error("Bulk Assign: Error attach materials", [
                            'assignment_id' => $assignment->id,
                            'employee_id' => $employeeId,
                            'material_ids' => $request->material_ids,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                } else {
                    $errors[] = "Assignment ID {$assignment->id} untuk employee ID {$employeeId}: Material IDs kosong atau tidak valid. Type: " . gettype($request->material_ids) . ", Value: " . json_encode($request->material_ids);
                    Log::error("Bulk Assign: Material IDs tidak valid", [
                        'assignment_id' => $assignment->id,
                        'employee_id' => $employeeId,
                        'material_ids' => $request->material_ids,
                        'type' => gettype($request->material_ids)
                    ]);
                }

                $assignments[] = $assignment;
            } catch (\Exception $e) {
                $errors[] = "Gagal membuat assignment untuk employee ID {$employeeId}: " . $e->getMessage();
                Log::error("Bulk Assign: Error create assignment", [
                    'employee_id' => $employeeId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        if (count($assignments) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat semua assignment.',
                'errors' => $errors
            ], 400);
        }

        $message = count($assignments) . ' training assignment berhasil dibuat dengan kode sesi: ' . $sessionCode;
        if (!empty($errors)) {
            $message .= ' Namun ada beberapa peringatan: ' . implode('; ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= ' dan ' . (count($errors) - 3) . ' peringatan lainnya.';
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $assignments,
            'session_code' => $sessionCode,
            'warnings' => $errors
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

    /**
     * View all assignments in a session
     *
     * @param string $sessionCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewSession($sessionCode)
    {
        $assignments = TrainingAssignment::with(['training', 'employee'])
            ->withCount('materials')
            ->where('session_code', $sessionCode)
            ->get();

        $assignmentsData = $assignments->map(function($assignment) {
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
            $statusBadge = '<span class="badge badge-'.$color.'">'.$label.'</span>';

            $progress = $assignment->progress_percentage ?? 0;
            $progressColor = $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning');
            $progressHtml = '
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-'.$progressColor.'" role="progressbar"
                         style="width: '.$progress.'%"
                         aria-valuenow="'.$progress.'"
                         aria-valuemin="0" aria-valuemax="100">
                        '.number_format($progress, 1).'%
                    </div>
                </div>';

            return [
                'id' => $assignment->id,
                'employee_name' => $assignment->employee ? $assignment->employee->name : '-',
                'status_badge' => $statusBadge,
                'progress_html' => $progressHtml,
                'materials_count' => $assignment->materials_count ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'session_code' => $sessionCode,
            'assignments' => $assignmentsData,
            'total' => $assignments->count()
        ]);
    }

    /**
     * Delete all assignments in a session
     *
     * @param string $sessionCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySession($sessionCode)
    {
        $assignments = TrainingAssignment::where('session_code', $sessionCode)->get();
        
        if ($assignments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan.'
            ], 404);
        }

        $count = 0;
        foreach ($assignments as $assignment) {
            // Detach materials
            $assignment->materials()->detach();
            
            // Delete related progress
            $assignment->progress()->delete();
            
            // Delete assignment
            $assignment->delete();
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => $count . ' assignment dalam sesi ' . $sessionCode . ' berhasil dihapus.'
        ]);
    }

    /**
     * Start a single assignment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function start($id)
    {
        $assignment = TrainingAssignment::findOrFail($id);
        
        // Validasi: hanya bisa start jika belum dibuka (is_opened = false) dan sudah mencapai start_date
        $today = Carbon::now()->toDateString();
        $startDate = $assignment->start_date ? $assignment->start_date->format('Y-m-d') : null;
        
        if ($assignment->is_opened) {
            return response()->json([
                'success' => false,
                'message' => 'Training ini sudah dibuka.'
            ], 400);
        }
        
        if ($startDate && $startDate > $today) {
            return response()->json([
                'success' => false,
                'message' => 'Training belum bisa dibuka. Tanggal mulai: ' . $assignment->start_date->format('d M Y')
            ], 400);
        }
        
        $assignment->update([
            'is_opened' => true,
            'opened_at' => now(),
            'start_date' => $startDate ?: now()->toDateString(), // Set start_date jika belum ada
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Training berhasil dimulai.',
            'data' => $assignment
        ]);
    }

    /**
     * Start all assignments in a session
     *
     * @param string $sessionCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function startSession($sessionCode)
    {
        $assignments = TrainingAssignment::where('session_code', $sessionCode)->get();
        
        if ($assignments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan.'
            ], 404);
        }
        
        $today = Carbon::now()->toDateString();
        $startedCount = 0;
        $errors = [];
        
        foreach ($assignments as $assignment) {
            $startDate = $assignment->start_date ? $assignment->start_date->format('Y-m-d') : null;
            
            // Cek apakah sudah dibuka
            if ($assignment->is_opened) {
                $errors[] = 'Assignment untuk ' . ($assignment->employee ? $assignment->employee->name : 'ID ' . $assignment->employee_id) . ' sudah dibuka.';
                continue;
            }
            
            if ($startDate && $startDate > $today) {
                $errors[] = 'Assignment untuk ' . ($assignment->employee ? $assignment->employee->name : 'ID ' . $assignment->employee_id) . ' belum bisa dibuka. Tanggal mulai: ' . $assignment->start_date->format('d M Y');
                continue;
            }
            
            $assignment->update([
                'is_opened' => true,
                'opened_at' => now(),
                'start_date' => $startDate ?: now()->toDateString(), // Set start_date jika belum ada
            ]);
            $startedCount++;
        }
        
        if ($startedCount > 0) {
            $message = $startedCount . ' assignment dalam sesi ' . $sessionCode . ' berhasil dimulai.';
            if (!empty($errors)) {
                $message .= ' ' . implode(' ', $errors);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'started_count' => $startedCount,
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada assignment yang bisa dimulai. ' . implode(' ', $errors),
                'errors' => $errors
            ], 400);
        }
    }

    /**
     * Generate unique session code for bulk assign
     *
     * @param int $trainingId
     * @return string
     */
    protected function generateSessionCode($trainingId)
    {
        $prefix = 'BULK-';
        $date = date('Ymd'); // Format: 20260210
        $time = date('His'); // Format: 140530
        
        // Get training code if available
        $training = TrainingMaster::on('pgsql2')->find($trainingId);
        $trainingCode = $training ? substr(str_replace([' ', '-'], '', $training->training_code ?? ''), 0, 6) : 'TRG';
        
        // Generate sequence number for today
        $lastSession = TrainingAssignment::where('session_code', 'like', $prefix . $date . '-' . $trainingCode . '%')
            ->orderBy('session_code', 'desc')
            ->first();
        
        if ($lastSession && $lastSession->session_code) {
            // Extract sequence from last session code
            $parts = explode('-', $lastSession->session_code);
            $lastSequence = isset($parts[3]) ? intval($parts[3]) : 0;
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        // Format: BULK-20260210-TRG-001
        return $prefix . $date . '-' . strtoupper($trainingCode) . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
