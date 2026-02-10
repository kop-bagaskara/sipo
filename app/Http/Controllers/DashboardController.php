<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanFirstProduction;
use App\Models\Machine;
use App\Models\Material;
use App\Models\User;
use App\Models\JobPrepress;
use App\Models\HandlingJobPrepress;
use App\Models\AssignJobPrepress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\EmployeeRequest;
use App\Models\OvertimeEmployee;
use App\Models\SplRequest;


class DashboardController extends Controller
{
    public function index()
    {
        $user = User::leftJoin('tb_divisis', 'users.divisi', '=', 'tb_divisis.id')
            ->select('users.*', 'tb_divisis.divisi as divisi_name')
            ->where('users.id', Auth::user()->id)
            ->first();

        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil data plan hari ini dari tb_plan_first_productions (Cached 1 menit)
        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $todayPlans = Cache::remember('dashboard.today_plans.' . $today->format('Y-m-d'), 60, function () use ($today, $todayEnd) {
            return PlanFirstProduction::where('start_jam', '<=', $todayEnd)
                ->where('end_jam', '>=', $today)
                ->orderBy('start_jam', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        });

        // Hitung progress
        $totalPlans = $todayPlans->count();
        $completedPlans = $todayPlans->where('flag_status', 'completed')->count();
        $inProgressPlans = $todayPlans->where('flag_status', 'in_progress')->count();
        $pendingPlans = $todayPlans->where('flag_status', 'pending')->count();
        $scheduledPlans = $todayPlans->where('flag_status', null)->count();
        $progressPercentage = $totalPlans > 0 ? round(($completedPlans / $totalPlans) * 100) : 0;

        // Hitung Machine Utilization dari tb_plan_first_productions (Cached 5 menit)
        $machineUtilization = Cache::remember('dashboard.machine_utilization', 300, function () {
            return $this->calculateMachineUtilization();
        });

        // Hitung Weekly Production Trend (Cached 10 menit)
        $weeklyTrend = Cache::remember('dashboard.weekly_trend', 600, function () {
            return $this->calculateWeeklyTrend();
        });

        // Hitung Prepress Data dari tb_job_prepresses (Cached 2 menit)
        $prepressData = Cache::remember('dashboard.prepress_data', 120, function () {
            return $this->calculatePrepressData();
        });

        // Data mesin paling sibuk dari plan yang sudah finish (Cached 5 menit)
        $busiestMachines = Cache::remember('dashboard.busiest_machines', 300, function () {
            return $this->getBusiestMachinesFromFinishedPlans();
        });

        // Data order terbanyak (Cached 5 menit)
        $topOrders = Cache::remember('dashboard.top_orders', 300, function () {
            return $this->getTopOrders();
        });

        return view('main.dashboard', compact(
            'user',
            'todayPlans',
            'totalPlans',
            'completedPlans',
            'inProgressPlans',
            'pendingPlans',
            'scheduledPlans',
            'progressPercentage',
            'machineUtilization',
            'weeklyTrend',
            'prepressData',
            'busiestMachines',
            'topOrders'
        ));
    }

    /**
     * Dashboard PPIC - Only load PPIC related data
     */
    public function ppic()
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect()->route('login');

        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $todayPlans = Cache::remember('dashboard.today_plans.' . $today->format('Y-m-d'), 60, function () use ($today, $todayEnd) {
            return PlanFirstProduction::where('start_jam', '<=', $todayEnd)
                ->where('end_jam', '>=', $today)
                ->orderBy('start_jam', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        });

        $totalPlans = $todayPlans->count();
        $completedPlans = $todayPlans->where('flag_status', 'completed')->count();
        $inProgressPlans = $todayPlans->where('flag_status', 'in_progress')->count();
        $pendingPlans = $todayPlans->where('flag_status', 'pending')->count();
        $scheduledPlans = $todayPlans->where('flag_status', null)->count();
        $progressPercentage = $totalPlans > 0 ? round(($completedPlans / $totalPlans) * 100) : 0;

        $machineUtilization = Cache::remember('dashboard.machine_utilization', 300, function () {
            return $this->calculateMachineUtilization();
        });

        $weeklyTrend = Cache::remember('dashboard.weekly_trend', 600, function () {
            return $this->calculateWeeklyTrend();
        });

        return view('main.dashboards.dashboard-ppic', compact(
            'user',
            'todayPlans',
            'totalPlans',
            'completedPlans',
            'inProgressPlans',
            'pendingPlans',
            'scheduledPlans',
            'progressPercentage',
            'machineUtilization',
            'weeklyTrend'
        ));
    }

    /**
     * Dashboard Prepress - Only load Prepress data
     */
    public function prepress()
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect()->route('login');

        $prepressData = Cache::remember('dashboard.prepress_data', 120, function () {
            return $this->calculatePrepressData();
        });

        return view('main.dashboards.dashboard-prepress', compact('user', 'prepressData'));
    }

    /**
     * Dashboard Development - Only load Development data
     */
    public function development()
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect()->route('login');

        return view('main.dashboards.dashboard-development', compact('user'));
    }

    /**
     * Dashboard Security - Only load Security data
     */
    public function security()
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect()->route('login');

        return view('main.dashboards.dashboard-security', compact('user'));
    }

    /**
     * Dashboard Supplier - Only load Supplier data
     */
    public function supplier()
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect()->route('login');

        return view('main.dashboards.dashboard-supplier', compact('user'));
    }

    /**
     * Helper: Get authenticated user with division info
     */
    private function getAuthUser()
    {
        return User::leftJoin('tb_divisis', 'users.divisi', '=', 'tb_divisis.id')
            ->select('users.*', 'tb_divisis.divisi as divisi_name')
            ->where('users.id', Auth::user()->id)
            ->first();
    }

    /**
     * Hitung Machine Utilization berdasarkan data dari tb_plan_first_productions
     */
    private function calculateMachineUtilization()
    {
        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        // Ambil data mesin yang digunakan hari ini (1 query aggregate)
        $machineData = PlanFirstProduction::where('start_jam', '<=', $todayEnd)
            ->where('end_jam', '>=', $today)
            ->whereNotNull('code_machine')
            ->groupBy('code_machine')
            ->select('code_machine', DB::raw('COUNT(*) as plan_count'))
            ->pluck('plan_count', 'code_machine');

        // Ambil nama mesin dari master machine (batch load, avoid N+1)
        $machinesByCode = collect();
        if ($machineData->isNotEmpty()) {
            $machinesByCode = Machine::whereIn('Code', $machineData->keys())->get()->keyBy('Code');
        }

        $machineUtilization = [];
        foreach ($machineData as $machineCode => $planCount) {
            $machine = $machinesByCode->get($machineCode);
            if ($machine) {
                // Hitung utilization berdasarkan jumlah plan vs kapasitas mesin
                $capacity = $machine->CapacityPerHour ?? 100; // Default capacity
                $utilization = min(100, ($planCount / max(1, $capacity)) * 100);

                $machineUtilization[] = [
                    'name' => $machine->Description ?? $machineCode,
                    'code' => $machineCode,
                    'utilization' => round($utilization, 1),
                    'planCount' => $planCount,
                    'capacity' => $capacity
                ];
            }
        }

        // Sort berdasarkan utilization tertinggi
        usort($machineUtilization, function ($a, $b) {
            return $b['utilization'] <=> $a['utilization'];
        });

        // Ambil top 5 mesin dengan utilization tertinggi
        return array_slice($machineUtilization, 0, 5);
    }

    /**
     * Hitung Weekly Production Trend
     * OPTIMIZATION: Gunakan 1 query, lalu filter di memory untuk menghindari 12 query terpisah
     */
    private function calculateWeeklyTrend()
    {
        $startOfWeek = Carbon::now()->startOfWeek();

        // Generate data untuk 4 minggu terakhir
        $weeklyRanges = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $startOfWeek->copy()->subWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weeklyRanges[] = [
                'start' => $weekStart,
                'end' => $weekEnd,
                'label' => 'Week ' . (4 - $i)
            ];
        }

        // OPTIMIZATION: Query semua data sekaligus, lalu filter di memory
        $firstWeekStart = $weeklyRanges[0]['start'];
        $lastWeekEnd = $weeklyRanges[3]['end'];

        $allPlans = PlanFirstProduction::whereBetween('start_jam', [$firstWeekStart, $lastWeekEnd])
            ->whereIn('flag_status', ['completed', 'in_progress', 'pending'])
            ->select('start_jam', 'flag_status')
            ->get();

        // Initialize weekly data
        $weeklyData = [];
        foreach ($weeklyRanges as $range) {
            $weekPlans = $allPlans->filter(function($plan) use ($range) {
                return $plan->start_jam >= $range['start'] && $plan->start_jam <= $range['end'];
            });

            $weeklyData[] = [
                'label' => $range['label'],
                'completed' => $weekPlans->where('flag_status', 'completed')->count(),
                'inProgress' => $weekPlans->where('flag_status', 'in_progress')->count(),
                'pending' => $weekPlans->where('flag_status', 'pending')->count()
            ];
        }

        return $weeklyData;
    }

    /**
     * Hitung Prepress Data dari tb_job_prepresses dan tb_handling_job_prepresses
     */
    private function calculatePrepressData()
    {
        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        // Ambil job prepress yang aktif (belum closed) - LIMIT untuk performa dashboard
        // Dashboard tidak perlu load semua job, cukup yang terbaru/relevan
        // Eager load assignJobPrepress untuk mengambil data PIC dari tb_assign_job_prepresses
        $todayJobs = JobPrepress::with(['assignJobPrepress'])
            ->whereNotIn('status_job', ['CLOSED'])
            ->orderBy('created_at', 'desc')
            ->limit(200) // Limit untuk performa: dashboard cukup tampilkan 200 job terbaru
            ->get();

        // OPTIMIZATION: Batch query untuk User mapping (fix N+1 query)
        // Kumpulkan semua created_by names yang unik
        $createdByNames = $todayJobs->pluck('created_by')
            ->filter()
            ->unique()
            ->map(function($name) {
                return strtolower(trim($name));
            })
            ->values();

        // Batch query User yang benar-benar dibutuhkan (hindari load semua user)
        $usersMap = collect();
        if ($createdByNames->isNotEmpty()) {
            $matchedUsers = User::with('divisiUser')
                ->whereIn(DB::raw('LOWER(TRIM(name))'), $createdByNames->all())
                ->get();

            $usersMap = $matchedUsers->keyBy(function($user) {
                return strtolower(trim($user->name));
            });
        }

        // Inisialisasi counter stats
        $prepressStats = [
            'total' => 0,
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'urgent' => 0,
            'rejected' => 0,
            'finish' => 0,
            'approved' => 0,
            'open' => 0,
            'plan' => 0,
            'closed' => 0,
            'assigned' => 0
        ];

        // Pisahkan berdasarkan departemen
        $marketingJobs = [];
        $otherJobs = [];

        foreach ($todayJobs as $job) {
            // Ambil status langsung dari job (tidak perlu query ulang)
            $status = $job->status_job ?? 'PENDING';
            $priority = $job->prioritas_job ?? null;

            // Normalisasi status (handle kedua format: dengan space dan underscore)
            $statusNormalized = strtoupper(trim($status));
            // Convert underscore to space untuk konsistensi, tapi handle "IN_PROGRESS" -> "IN PROGRESS"
            $statusNormalized = str_replace('_', ' ', $statusNormalized);
            // Normalize multiple spaces to single space
            $statusNormalized = preg_replace('/\s+/', ' ', $statusNormalized);

            // Mapping status ke kategori dashboard
            $mappedStatus = 'pending';

            // FINISH - Job selesai (Counter Finish menghitung FINISH)
            if ($statusNormalized === 'FINISH') {
                $mappedStatus = 'finish';
                $prepressStats['finish']++; // Counter Finish menghitung FINISH
            }
            // COMPLETED - Job selesai (Counter Finish menghitung COMPLETED)
            elseif ($statusNormalized === 'COMPLETED') {
                $mappedStatus = 'finish';
                $prepressStats['finish']++; // Counter Finish menghitung COMPLETED
            }
            // IN PROGRESS - Sedang dikerjakan
            elseif ($statusNormalized === 'IN PROGRESS' || $statusNormalized === 'IN_PROGRESS') {
                $mappedStatus = 'in_progress';
                $prepressStats['in_progress']++;
            }
            // OPEN / ASSIGNED - Job baru/belum dikerjakan atau sudah di-assign
            elseif ($statusNormalized === 'OPEN' || $statusNormalized === 'ASSIGNED') {
                $mappedStatus = 'assigned';
                $prepressStats['assigned']++;
            }
            // PLAN - Sudah dijadwalkan (Counter Approved menghitung PLAN)
            elseif ($statusNormalized === 'PLAN') {
                $mappedStatus = 'plan';
                $prepressStats['approved']++; // Counter Approved menghitung PLAN
            }
            // APPROVED - Sudah disetujui
            elseif ($statusNormalized === 'APPROVED') {
                $mappedStatus = 'approved';
                $prepressStats['approved']++;
            }
            // REJECT / DISSAPPROVE - Ditolak
            elseif (in_array($statusNormalized, ['REJECT', 'DISSAPPROVE', 'REJECTED'])) {
                $mappedStatus = 'rejected';
                $prepressStats['rejected']++;
            }
            // CLOSED - Ditutup
            elseif ($statusNormalized === 'CLOSED') {
                $mappedStatus = 'closed';
                $prepressStats['closed']++;
            }
            // PENDING / lainnya
            else {
                $mappedStatus = 'pending';
                $prepressStats['pending']++;
            }

            // Check priority untuk urgent (priority <= 3)
            if ($priority !== null && is_numeric($priority) && $priority <= 3) {
                $prepressStats['urgent']++;
            }

            $prepressStats['total']++;

            // Ambil assignee/PIC info dari tb_assign_job_prepresses melalui relationship (sudah di-eager load)
            // Relationship: JobPrepress->assignJobPrepress() -> AssignJobPrepress model -> tb_assign_job_prepresses table
            $assignee = $job->assignJobPrepress;

            // Ambil user yang membuat job untuk cek departemen dari pre-loaded users map
            $createdByUser = null;
            $userDivisiId = null;
            $userDepartmentName = null;

            if ($job->created_by) {
                // Ambil dari pre-loaded users map (tidak perlu query lagi)
                $createdByUser = $usersMap->get(strtolower(trim($job->created_by)));

                if ($createdByUser) {
                    $userDivisiId = $createdByUser->divisi;
                    $userDepartmentName = $createdByUser->divisiUser
                        ? $createdByUser->divisiUser->divisi
                        : null;
                }
            }

            // REMOVED: Duplicate query - sudah di-handle di line 324-334 dengan pre-loaded usersMap

            $jobData = [
                'id' => $job->id,
                'nomor_job_order' => $job->nomor_job_order,
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design,
                'status' => $mappedStatus,
                'job' => $job->job_order,
                'status_handling' => $status,
                'priority' => $priority,
                'tanggal_job_order' => $job->tanggal_job_order,
                'tanggal_deadline' => $job->tanggal_deadline,
                // Data PIC/Pelaksana diambil dari tb_assign_job_prepresses
                // name_user_pic: nama PIC dari tabel tb_assign_job_prepresses
                'assignee' => $assignee ? $assignee->name_user_pic : null,
                // est_waktu_job: estimasi waktu job dari tabel tb_assign_job_prepresses
                'est_waktu_job' => $assignee ? $assignee->est_waktu_job : null,
                // id_user_pic: ID user PIC dari tabel tb_assign_job_prepresses (untuk referensi)
                'id_user_pic' => $assignee ? $assignee->id_user_pic : null,
                'department' => $userDepartmentName,
                'created_by' => $job->created_by
            ];

            // Pisahkan berdasarkan departemen: Marketing = divisi 2
            if ($userDivisiId == 2) {
                $marketingJobs[] = $jobData;
            } else {
                $otherJobs[] = $jobData;
            }
        }

        // dd($marketingJobs);

        // Sort berdasarkan priority dan deadline untuk masing-masing kategori
        usort($marketingJobs, function ($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] <=> $b['priority'];
            }
            return strtotime($a['tanggal_deadline']) <=> strtotime($b['tanggal_deadline']);
        });

        usort($otherJobs, function ($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] <=> $b['priority'];
            }
            return strtotime($a['tanggal_deadline']) <=> strtotime($b['tanggal_deadline']);
        });

        // Hitung stats per departemen
        $marketingStats = $this->calculateDepartmentStats($marketingJobs);


        // dd($marketingStats);
        $otherStats = $this->calculateDepartmentStats($otherJobs);

        // Ambil recent jobs untuk list
        $recentJobs = JobPrepress::with(['handlingJobPrepress' => function ($query) {
            $query->latest();
        }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();



        // Filter jobs untuk ditampilkan: hanya yang statusnya 'in_progress'
        $marketingJobsInProgress = array_filter($marketingJobs, function($job) {
            return $job['status'] === 'in_progress';
        });

        $otherJobsInProgress = array_filter($otherJobs, function($job) {
            return $job['status'] === 'in_progress';
        });

        return [
            'stats' => $prepressStats,
            'jobs' => array_slice(array_merge($marketingJobs, $otherJobs), 0, 10), // Top 10 jobs overall
            'recent_jobs' => $recentJobs, // Recent jobs untuk list
            'departments' => [
                'marketing' => [
                    'jobs' => array_values(array_slice($marketingJobsInProgress, 0, 8)), // Top 8 marketing jobs IN PROGRESS
                    'all_jobs' => $marketingJobs, // Semua jobs untuk pencarian
                    'stats' => $marketingStats
                ],
                'others' => [
                    'jobs' => array_values(array_slice($otherJobsInProgress, 0, 8)), // Top 8 other jobs IN PROGRESS
                    'all_jobs' => $otherJobs, // Semua jobs untuk pencarian
                    'stats' => $otherStats
                ]
            ]
        ];
    }

    /**
     * Hitung stats untuk departemen tertentu
     */
    private function calculateDepartmentStats($jobs)
    {
        $stats = [
            'total' => 0,
            // 'total' => ($jobs['completed']) + count($jobs['in_progress']) + count($jobs['open']) + count($jobs['plan']),
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'urgent' => 0,
            'rejected' => 0,
            'finish' => 0,
            'approved' => 0,
            'open' => 0,
            'plan' => 0,
            'closed' => 0,
            'assigned' => 0
        ];

        foreach ($jobs as $job) {
            switch ($job['status']) {
                case 'completed':
                    // COMPLETED masuk ke finish counter
                    $stats['finish']++;
                    break;
                case 'in_progress':
                    $stats['in_progress']++;
                    break;
                case 'plan':
                    // PLAN masuk ke approved counter
                    $stats['approved']++;
                    break;
                case 'open':
                    // OPEN status masuk ke assigned counter
                    $stats['assigned']++;
                    break;
                case 'assigned':
                    $stats['assigned']++;
                    break;
                case 'approved':
                    $stats['approved']++;
                    break;
                case 'finish':
                    // FINISH masuk ke finish counter
                    $stats['finish']++;
                    break;
                default:
                    $stats['pending']++;
                    break;
            }

            if (isset($job['priority']) && $job['priority'] !== null && is_numeric($job['priority']) && $job['priority'] <= 3) {
                $stats['urgent']++;
            }
        }

        // Hitung total dari semua status yang relevan untuk dashboard
        // Total = finish + in_progress + assigned + approved
        $stats['total'] = $stats['finish'] + $stats['in_progress'] + $stats['assigned'] + $stats['approved'];

        return $stats;
    }

    /**
     * Ambil mesin paling sibuk dari plan yang sudah finish
     */
    private function getBusiestMachinesFromFinishedPlans()
    {
        try {
            // Ambil data plan yang sudah finish
            // Cek status: completed, FINISH, atau FINISHED
            // Coba ambil dari 30 hari terakhir dulu, jika kosong ambil semua data
            $thirtyDaysAgo = Carbon::now()->subDays(30);

            $finishedPlans = PlanFirstProduction::whereIn('flag_status', ['completed', 'FINISH', 'FINISHED'])
                ->whereNotNull('code_machine')
                ->where('updated_at', '>=', $thirtyDaysAgo) // Filter 30 hari terakhir
                ->select('code_machine', DB::raw('COUNT(*) as finished_count'))
                ->groupBy('code_machine')
                ->orderBy('finished_count', 'desc')
                ->limit(5)
                ->get();

            // Jika tidak ada data dalam 30 hari terakhir, ambil semua data
            if ($finishedPlans->isEmpty()) {
                $finishedPlans = PlanFirstProduction::whereIn('flag_status', ['completed', 'FINISH', 'FINISHED'])
                    ->whereNotNull('code_machine')
                    ->select('code_machine', DB::raw('COUNT(*) as finished_count'))
                    ->groupBy('code_machine')
                    ->orderBy('finished_count', 'desc')
                    ->limit(5)
                    ->get();
            }

            // OPTIMIZATION: Eager load semua Machine sekaligus
            $machineCodes = $finishedPlans->pluck('code_machine')->filter()->unique()->toArray();
            $machines = Machine::whereIn('Code', $machineCodes)
                ->get()
                ->keyBy('Code');

            $busiestMachines = [];
            foreach ($finishedPlans as $plan) {
                $machine = $machines->get($plan->code_machine);
                if ($machine) {
                    $busiestMachines[] = [
                        'name' => $machine->Description ?? $plan->code_machine,
                        'code' => $plan->code_machine,
                        'finished_count' => $plan->finished_count,
                    ];
                }
            }

            return $busiestMachines;
        } catch (\Exception $e) {
            Log::error('Error getting busiest machines: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil order terbanyak (dari order_tb)
     */
    private function getTopOrders()
    {
        try {
            $topOrders = [];

            // Cek dari mysql8 (mesin selain CD6)
            try {
                $ordersMysql8 = DB::connection('mysql8')
                    ->table('order_tb')
                    ->select('jo', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(totprod) as total_production'))
                    ->where('status', 'ORDER')
                    ->groupBy('jo')
                    ->orderBy('order_count', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($ordersMysql8 as $order) {
                    $topOrders[] = [
                        'jo' => $order->jo,
                        'order_count' => $order->order_count,
                        'total_production' => $order->total_production ?? 0,
                        'source' => 'mysql8'
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Error getting orders from mysql8: ' . $e->getMessage());
            }

            // Cek dari mysql9 (mesin CD6)
            try {
                $ordersMysql9 = DB::connection('mysql9')
                    ->table('order_tb')
                    ->select('jo', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(totprod) as total_production'))
                    ->where('status', 'ORDER')
                    ->groupBy('jo')
                    ->orderBy('order_count', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($ordersMysql9 as $order) {
                    $topOrders[] = [
                        'jo' => $order->jo,
                        'order_count' => $order->order_count,
                        'total_production' => $order->total_production ?? 0,
                        'source' => 'mysql9'
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Error getting orders from mysql9: ' . $e->getMessage());
            }

            // Sort berdasarkan order_count dan ambil top 5
            usort($topOrders, function ($a, $b) {
                return $b['order_count'] <=> $a['order_count'];
            });

            return array_slice($topOrders, 0, 5);
        } catch (\Exception $e) {
            Log::error('Error getting top orders: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * API endpoint untuk mendapatkan data prepress real-time
     */
    public function getPrepressData()
    {
        try {
            $prepressData = $this->calculatePrepressData();
            return response()->json([
                'success' => true,
                'data' => $prepressData,
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexMonitoringSO()
    {
        return view('main.process.monitoring-so');
    }

    /**
     * Dashboard khusus untuk karyawan (divisi 7)
     */
    public function dashboardKaryawan(Request $request)
    {
        $user = Auth::user();
        $query = EmployeeRequest::query();

        // Filter berdasarkan role user
        if ($user->isHR()) {
            // HR bisa lihat semua pengajuan
            $query->whereIn('status', [
                EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ($user->supervisor_id) {
            // Supervisor bisa lihat pengajuan dari bawahannya
            $query->where('supervisor_id', $user->id);
        } else {
            // Karyawan biasa hanya bisa lihat pengajuan sendiri
            $query->where('employee_id', $user->id);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis pengajuan
        if ($request->has('type') && $request->type !== '') {
            $query->where('request_type', $request->type);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter type and date filters from request
        $filterType = $request->get('filter_type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        // Load overtime entries if needed
        $overtimeEntries = collect();
        if ($filterType === '' || $filterType === 'overtime') {
            $overtimeQuery = \App\Models\OvertimeEntry::where(function($query) use ($user) {
                $query->where('employee_id', $user->id)
                    ->orWhere('divisi_id', $user->divisi);
            });

            if ($dateFrom) {
                $overtimeQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $overtimeQuery->whereDate('created_at', '<=', $dateTo);
            }

            $overtimeEntries = $overtimeQuery->orderBy('created_at', 'desc')->get();
        }

        // Load vehicle requests if needed
        $vehicleRequests = collect();
        if ($filterType === '' || $filterType === 'vehicle') {
            $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $vehicleQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $vehicleQuery->whereDate('request_date', '<=', $dateTo);
            }

            $vehicleRequests = $vehicleQuery->orderBy('created_at', 'desc')->get();
        }

        // Load asset requests if needed
        $assetRequests = collect();
        if ($filterType === '' || $filterType === 'asset') {
            $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $assetQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $assetQuery->whereDate('request_date', '<=', $dateTo);
            }

            $assetRequests = $assetQuery->orderBy('created_at', 'desc')->get();
        }

        // Load SPL requests if needed
        $splRequests = collect();
        if ($filterType === '' || $filterType === 'spl') {
            $splQuery = SplRequest::where(function($query) use ($user) {
                if ($user->isHR()) {
                    // HR bisa lihat semua SPL
                } else {
                    // Supervisor hanya bisa lihat SPL yang dibuatnya atau divisinya
                    $query->where('supervisor_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                }
            });

            if ($dateFrom) {
                $splQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $splQuery->whereDate('request_date', '<=', $dateTo);
            }

            $splRequests = $splQuery->with('supervisor', 'employees')->orderBy('created_at', 'desc')->get();
        }

        // Get statistics for dashboard
        $stats = $this->getDashboardStats($user);

        // Get departments for filter
        $departments = DB::connection('mysql7')
            ->table('masterdivisi')
            ->where('Begda', '<=', now())
            ->where(function($q) {
                $q->whereNull('Endda')
                  ->orWhere('Endda', '>=', now());
            })
            ->select('Kode Divisi as id', 'Nama Divisi as name')
            ->orderBy('Nama Divisi')
            ->get();

        return view('dashboard-karyawan', compact('requests', 'stats', 'filterType', 'dateFrom', 'dateTo', 'overtimeEntries', 'vehicleRequests', 'assetRequests', 'splRequests', 'departments'));
    }

        private function getDashboardStats($user)
    {
        // Get employee IDs in same division
        $employeeIds = User::where('divisi', $user->divisi)->pluck('id');

        $totalPending = 0;

        // For HEAD/SPV/MANAGER: Count berdasarkan approval timestamps yang NULL
        if (method_exists($user, 'canApprove') && $user->canApprove()) {
            // Get approval flows
            // Untuk absence, gunakan divisi user untuk mendapatkan approval flow yang sesuai
            $absenceFlow = \App\Models\ApprovalSetting::getApprovalFlow('absence', $user->divisi);
            $shiftChangeFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');

            // Cek apakah HEAD ada di urutan approval
            $headApprovalOrderAbsence = null;
            $headApprovalOrderShiftChange = null;

            foreach ($absenceFlow as $setting) {
                if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user)) {
                    $headApprovalOrderAbsence = $setting->approval_order;
                    break;
                }
            }

            foreach ($shiftChangeFlow as $setting) {
                if ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user)) {
                    $headApprovalOrderShiftChange = $setting->approval_order;
                    break;
                }
            }

            // Count Form: head_approved_at NULL
            $formQuery = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
                ->whereIn('employee_id', $employeeIds)
                ->whereNull('head_approved_at')
                ->whereNull('head_rejected_at')
                ->where(function($q) use ($headApprovalOrderAbsence, $headApprovalOrderShiftChange) {
                    $q->where(function($subQ) use ($headApprovalOrderAbsence) {
                        if ($headApprovalOrderAbsence == 1) {
                            $subQ->where('request_type', 'absence')
                                ->whereNull('supervisor_approved_at')
                                ->whereNull('supervisor_rejected_at');
                        } elseif ($headApprovalOrderAbsence == 2) {
                            $subQ->where('request_type', 'absence')
                                ->whereNotNull('supervisor_approved_at');
                        }
                    })->orWhere(function($subQ) use ($headApprovalOrderShiftChange) {
                        if ($headApprovalOrderShiftChange == 1) {
                            $subQ->where('request_type', 'shift_change')
                                ->whereNull('supervisor_approved_at')
                                ->whereNull('supervisor_rejected_at');
                        } elseif ($headApprovalOrderShiftChange == 2) {
                            $subQ->where('request_type', 'shift_change')
                                ->whereNotNull('supervisor_approved_at');
                        }
                    });
                });

            $formPending = $formQuery->count();

            // Overtime: skip untuk sekarang, sistemnya nanti sendiri
            $overtimePending = 0;

            // Count Vehicle/Asset: manager_at NULL dan status pending_manager (untuk VehicleAssetRequest)
            $vehiclePending = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                ->where('divisi_id', $user->divisi)
                ->whereNull('manager_at')
                ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                ->count();

            $assetPending = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                ->where('divisi_id', $user->divisi)
                ->whereNull('manager_at')
                ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                ->count();

            $totalPending = $formPending + $overtimePending + $vehiclePending + $assetPending;
        } else {
            // For other users (HR, Employee), use existing logic
            $query = EmployeeRequest::query();

            if (method_exists($user, 'isHR') && $user->isHR()) {
                // HR can see all requests
                $query->whereIn('status', [
                    EmployeeRequest::STATUS_PENDING,
                    EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                    EmployeeRequest::STATUS_MANAGER_APPROVED,
                    EmployeeRequest::STATUS_MANAGER_REJECTED,
                    EmployeeRequest::STATUS_HR_APPROVED,
                    EmployeeRequest::STATUS_HR_REJECTED
                ]);
            } elseif (method_exists($user, 'hasSupervisor') && $user->hasSupervisor()) {
                // Supervisor can see requests from subordinates
                $query->where('supervisor_id', $user->id);
            } else {
                // Employee can see own requests
                $query->where('employee_id', $user->id);
            }

            $totalPending = $query->where('status', EmployeeRequest::STATUS_PENDING)->count();
        }

        // For other stats, use existing logic
        $query = EmployeeRequest::query();

        if (method_exists($user, 'isHR') && $user->isHR()) {
            // HR can see all requests
            $query->whereIn('status', [
                EmployeeRequest::STATUS_PENDING,
                EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                EmployeeRequest::STATUS_MANAGER_APPROVED,
                EmployeeRequest::STATUS_MANAGER_REJECTED,
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ((method_exists($user, 'hasSupervisor') && $user->hasSupervisor()) ||
                  (method_exists($user, 'canApprove') && $user->canApprove())) {
            // HEAD/SPV/MANAGER can see requests from their division
            $employeeIds = User::where('divisi', $user->divisi)->pluck('id');
            $query->whereIn('employee_id', $employeeIds);
        } else {
            // Employee can see own requests
            $query->where('employee_id', $user->id);
        }

        $totalRequests = $query->count();
        $approvedRequests = $query->where('status', EmployeeRequest::STATUS_HR_APPROVED)->count();
        $rejectedRequests = $query->whereIn('status', [
            EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
            EmployeeRequest::STATUS_MANAGER_REJECTED,
            EmployeeRequest::STATUS_HR_REJECTED
        ])->count();

        return [
            'total_requests' => $totalRequests,
            'pending_requests' => $totalPending,
            'approved_requests' => $approvedRequests,
            'rejected_requests' => $rejectedRequests
        ];
    }

    /**
     * AJAX endpoint untuk load data Overview tab (Machine Utilization, Weekly Trend, dll)
     * LAZY LOADING: Dipanggil saat user klik tab Overview
     */
    public function loadOverviewData()
    {
        try {
            // Hitung Machine Utilization dari tb_plan_first_productions (Cached 5 menit)
            $machineUtilization = Cache::remember('dashboard.machine_utilization', 300, function () {
                return $this->calculateMachineUtilization();
            });

            // Hitung Weekly Production Trend (Cached 10 menit)
            $weeklyTrend = Cache::remember('dashboard.weekly_trend', 600, function () {
                return $this->calculateWeeklyTrend();
            });

            // Data mesin paling sibuk dari plan yang sudah finish (Cached 5 menit)
            $busiestMachines = Cache::remember('dashboard.busiest_machines', 300, function () {
                return $this->getBusiestMachinesFromFinishedPlans();
            });

            // Data order terbanyak
            $topOrders = $this->getTopOrders();

            return response()->json([
                'success' => true,
                'data' => [
                    'machineUtilization' => $machineUtilization,
                    'weeklyTrend' => $weeklyTrend,
                    'busiestMachines' => $busiestMachines,
                    'topOrders' => $topOrders
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading overview data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data overview'
            ], 500);
        }
    }

    /**
     * AJAX endpoint untuk load data Prepress tab
     * LAZY LOADING: Dipanggil saat user klik tab Prepress
     */
    public function loadPrepressData()
    {
        try {
            // Hitung Prepress Data dari tb_job_prepresses (Cached 2 menit)
            $prepressData = Cache::remember('dashboard.prepress_data', 120, function () {
                return $this->calculatePrepressData();
            });

            return response()->json([
                'success' => true,
                'data' => $prepressData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading prepress data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data prepress'
            ], 500);
        }
    }
}
