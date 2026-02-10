<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingParticipant;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrainingDashboardController extends Controller
{
    /**
     * Display training dashboard
     */
    public function index()
    {
        // Get basic statistics
        $stats = [
            'total_trainings' => TrainingMaster::count(),
            'active_trainings' => TrainingMaster::where('status', 'active')->count(),
            'published_trainings' => TrainingMaster::where('status', 'published')->count(),
            'total_participants' => TrainingParticipant::count(),
            'pending_approvals' => TrainingParticipant::where('registration_status', 'registered')->count(),
            'completed_trainings' => TrainingParticipant::where('registration_status', 'completed')->count(),
            'certificates_issued' => TrainingParticipant::where('certificate_issued', true)->count()
        ];

        // Get training by type
        $trainingByType = TrainingMaster::selectRaw('training_type, COUNT(*) as count')
            ->groupBy('training_type')
            ->get()
            ->pluck('count', 'training_type');

        // Get training by status
        $trainingByStatus = TrainingMaster::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Get participant registration trends (last 30 days)
        $registrationTrends = TrainingParticipant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get approval trends (last 30 days)
        $approvalTrends = TrainingParticipant::selectRaw('DATE(approved_at) as date, COUNT(*) as count')
            ->where('registration_status', 'approved')
            ->where('approved_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get department participation (cross-connection safe)
        $participantUsers = TrainingParticipant::query()
            ->select(['employee_id'])
            ->get()
            ->pluck('employee_id')
            ->unique()
            ->values();

        $userDivisiMap = User::query()
            ->whereIn('id', $participantUsers)
            ->pluck('divisi', 'id');

        $divisiCounts = TrainingParticipant::query()
            ->get(['employee_id'])
            ->groupBy(function ($p) use ($userDivisiMap) {
                return $userDivisiMap[$p->employee_id] ?? null;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->filter(function ($count, $divisiId) {
                return !is_null($divisiId);
            });

        $divisiIds = $divisiCounts->keys()->filter()->values();
        $divisiNames = $divisiIds->isNotEmpty()
            ? Divisi::whereIn('id', $divisiIds)->pluck('divisi', 'id')
            : collect();

        $departmentParticipation = $divisiCounts
            ->map(function ($count, $divisiId) use ($divisiNames) {
                return (object) [
                    'divisi' => $divisiNames[$divisiId] ?? 'Unknown',
                    'count' => $count,
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->take(10);

        // Get recent trainings
        $recentTrainings = TrainingMaster::with(['creator', 'participants'])
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        // Get pending approvals
        $pendingApprovals = TrainingParticipant::with(['training', 'employee'])
            ->where('registration_status', 'registered')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top performing trainings
        $topTrainings = TrainingMaster::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->limit(5)
            ->get();

        // Get completion rates by department (cross-connection safe)
        $participants = TrainingParticipant::query()
            ->get(['employee_id', 'registration_status']);

        $divisiAgg = [];
        foreach ($participants as $p) {
            $divisiId = $userDivisiMap[$p->employee_id] ?? null;
            if ($divisiId === null) {
                continue;
            }
            if (!isset($divisiAgg[$divisiId])) {
                $divisiAgg[$divisiId] = ['total' => 0, 'completed' => 0];
            }
            $divisiAgg[$divisiId]['total'] += 1;
            if ($p->registration_status === 'completed') {
                $divisiAgg[$divisiId]['completed'] += 1;
            }
        }

        $completionRates = collect($divisiAgg)
            ->map(function ($agg, $divisiId) use ($divisiNames) {
                $rate = $agg['total'] > 0 ? round($agg['completed'] * 100.0 / $agg['total'], 2) : 0;
                return (object) [
                    'divisi' => $divisiNames[$divisiId] ?? 'Unknown',
                    'total_participants' => $agg['total'],
                    'completed_participants' => $agg['completed'],
                    'completion_rate' => $rate,
                ];
            })
            ->sortByDesc('completion_rate')
            ->values();

        // Department
        $departments = DB::connection('mysql7')->table('masterdivisi')->select('Kode Divisi as id', 'Nama Divisi as divisi')->get();

        return view('hr.training.dashboard', compact(
            'stats',
            'trainingByType',
            'trainingByStatus',
            'registrationTrends',
            'approvalTrends',
            'departmentParticipation',
            'recentTrainings',
            'pendingApprovals',
            'topTrainings',
            'completionRates',
            'departments'
        ));
    }

    /**
     * Get training statistics for API
     */
    public function getStats()
    {
        $stats = [
            'total_trainings' => TrainingMaster::count(),
            'active_trainings' => TrainingMaster::where('status', 'active')->count(),
            'published_trainings' => TrainingMaster::where('status', 'published')->count(),
            'total_participants' => TrainingParticipant::count(),
            'pending_approvals' => TrainingParticipant::where('registration_status', 'registered')->count(),
            'completed_trainings' => TrainingParticipant::where('registration_status', 'completed')->count(),
            'certificates_issued' => TrainingParticipant::where('certificate_issued', true)->count()
        ];

        return response()->json($stats);
    }

    /**
     * Get registration trends for charts
     */
    public function getRegistrationTrends(Request $request)
    {
        $days = $request->get('days', 30);

        $trends = TrainingParticipant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trends);
    }

    /**
     * Get approval trends for charts
     */
    public function getApprovalTrends(Request $request)
    {
        $days = $request->get('days', 30);

        $trends = TrainingParticipant::selectRaw('DATE(approved_at) as date, COUNT(*) as count')
            ->where('registration_status', 'approved')
            ->where('approved_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trends);
    }

    /**
     * Get department participation data
     */
    public function getDepartmentParticipation()
    {
        $employeeIds = TrainingParticipant::query()->pluck('employee_id')->unique()->values();
        $userDivisiMap = User::query()->whereIn('id', $employeeIds)->pluck('divisi', 'id');

        $divisiCounts = TrainingParticipant::query()
            ->get(['employee_id'])
            ->groupBy(function ($p) use ($userDivisiMap) {
                return $userDivisiMap[$p->employee_id] ?? null;
            })
            ->map(fn($g) => $g->count())
            ->filter(fn($count, $divisiId) => !is_null($divisiId));

        $divisiIds = $divisiCounts->keys()->filter()->values();
        $divisiNames = $divisiIds->isNotEmpty()
            ? Divisi::whereIn('id', $divisiIds)->pluck('divisi', 'id')
            : collect();

        $data = $divisiCounts->map(function ($count, $divisiId) use ($divisiNames) {
                return (object) ['divisi' => $divisiNames[$divisiId] ?? 'Unknown', 'count' => $count];
            })
            ->sortByDesc('count')
            ->values();

        return response()->json($data);
    }

    /**
     * Get training completion rates
     */
    public function getCompletionRates()
    {
        $participants = TrainingParticipant::query()->get(['employee_id', 'registration_status']);
        $userDivisiMap = User::query()->whereIn('id', $participants->pluck('employee_id')->unique())
            ->pluck('divisi', 'id');
        $divisiNames = Divisi::pluck('divisi', 'id');

        $agg = [];
        foreach ($participants as $p) {
            $divisiId = $userDivisiMap[$p->employee_id] ?? null;
            if ($divisiId === null) continue;
            $agg[$divisiId]['total'] = ($agg[$divisiId]['total'] ?? 0) + 1;
            $agg[$divisiId]['completed'] = ($agg[$divisiId]['completed'] ?? 0) + ($p->registration_status === 'completed' ? 1 : 0);
        }

        $rates = collect($agg)->map(function ($v, $divisiId) use ($divisiNames) {
            $rate = $v['total'] > 0 ? round($v['completed'] * 100.0 / $v['total'], 2) : 0;
            return (object) [
                'divisi' => $divisiNames[$divisiId] ?? 'Unknown',
                'total_participants' => $v['total'],
                'completed_participants' => $v['completed'],
                'completion_rate' => $rate,
            ];
        })->sortByDesc('completion_rate')->values();

        return response()->json($rates);
    }

    /**
     * Test database connection
     */
    public function testConnection()
    {
        try {
            $result = DB::connection('mysql7')->table('masterdivisi')->select('Kode Divisi as id', 'Nama Divisi as divisi')->limit(5)->get();
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee training history by department
     */
    public function getEmployeeTrainingHistory(Request $request)
    {
        try {
            $departmentId = $request->get('department_id');

            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department ID is required'
                ], 400);
            }

            $today = now()->format('Y-m-d');

            $query = DB::connection('mysql7')->table('masteremployee')
                ->where('Begda', '<=', $today)
                ->where(function($q) use ($today) {
                    $q->whereNull('Endda')
                      ->orWhere('Endda', '>=', $today);
                });

            // Jika bukan "all", filter berdasarkan departemen
            if ($departmentId !== 'all') {
                $query->where('Kode Divisi', $departmentId);
            }

            $employees = $query->distinct()->get();
            // Get employees from the department (using mysql7 connection)
            // $employees = User::on('mysql7')->where('divisi', $departmentId)->get();

            $employeeData = $employees->map(function ($employee) {
                // Get training participants using NIP connection - show present, attended, or completed
                $trainingParticipants = TrainingParticipant::where('employee_id', $employee->Nip)
                    ->where(function($query) {
                        $query->whereIn('registration_status', ['attended', 'completed'])
                              ->orWhere('attendance_status', 'present');
                    })
                    ->with('training')
                    ->get();

                $trainings = $trainingParticipants->map(function ($participant) {
                    return [
                        'training_name' => $participant->training->training_name ?? 'N/A',
                        'registration_status' => $participant->registration_status,
                        'attendance_status' => $participant->attendance_status,
                        'registered_at' => $participant->registered_at,
                        'completed_at' => $participant->completed_at
                    ];
                });
                // dd($employee);

                $kd_jabatan = $employee->{'Kode Bagian'};
                // dd($kd_jabatan);
                $nama_jabatan = DB::connection('mysql7')->table('masterbagian')->where('Kode Bagian', $kd_jabatan)->pluck('Nama Bagian')->first();

                // dd($nama_jabatan);

                return [
                    // 'id' => $employee->id,
                    'name' => $employee->Nama,
                    'nip' => $employee->Nip,
                    'position' => $nama_jabatan ?? 'N/A',
                    'trainings' => $trainings
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $employeeData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
