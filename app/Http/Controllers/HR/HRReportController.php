<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeRequest;
use App\Models\TrainingMaster;
use App\Models\TrainingSchedule;
use App\Models\TrainingParticipant;
use App\Models\SecurityVehicleChecklist;
use App\Models\SecurityGoodsMovement;
use App\Models\SecurityDailyActivityLog;
use App\Models\SecurityDailyActivityEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRReportController extends Controller
{
    /**
     * Display HR reports dashboard.
     */
    public function index()
    {
        // Get basic statistics
        $totalUsers = User::count();
        $totalSecurityUsers = User::where('divisi', 11)->count();
        $totalRequests = EmployeeRequest::count();
        $totalTrainings = TrainingMaster::count();

        // Get security statistics
        $totalVehicleChecklists = SecurityVehicleChecklist::count();
        $totalGoodsMovements = SecurityGoodsMovement::count();
        $totalDailyActivities = SecurityDailyActivityLog::count();
        $todayVehicleChecklists = SecurityVehicleChecklist::whereDate('tanggal', today())->count();
        $todayGoodsMovements = SecurityGoodsMovement::whereDate('tanggal', today())->count();
        $todayDailyActivities = SecurityDailyActivityLog::whereDate('tanggal', today())->count();

        // Get recent activities
        $recentRequests = EmployeeRequest::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTrainings = TrainingSchedule::with(['training'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('main.hr.reports.index', compact(
            'totalUsers',
            'totalSecurityUsers',
            'totalRequests',
            'totalTrainings',
            'totalVehicleChecklists',
            'totalGoodsMovements',
            'totalDailyActivities',
            'todayVehicleChecklists',
            'todayGoodsMovements',
            'todayDailyActivities',
            'recentRequests',
            'recentTrainings'
        ));
    }

    /**
     * User Report
     */
    public function userReport(Request $request)
    {
        $query = User::with(['divisiUser', 'jabatanUser', 'levelUser']);

        // Filter by divisi
        if ($request->has('divisi') && $request->divisi) {
            $query->where('divisi', $request->divisi);
        }

        // Filter by jabatan
        if ($request->has('jabatan') && $request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->orderBy('name')->get();

        // Get filter options
        $divisis = DB::table('tb_divisis')->get();
        $jabatans = DB::table('tb_jabatans')->get();
        $levels = DB::table('tb_levels')->get();

        return view('main.hr.reports.user-report', compact('users', 'divisis', 'jabatans', 'levels'));
    }

    /**
     * Security User Report
     */
    public function securityUserReport(Request $request)
    {
        $query = User::where('divisi', 11)
            ->with(['divisiUser', 'jabatanUser', 'levelUser']);

        // Filter by jabatan
        if ($request->has('jabatan') && $request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->orderBy('name')->get();

        // Get filter options
        $jabatans = DB::table('tb_jabatans')->get();
        $levels = DB::table('tb_levels')->get();

        return view('main.hr.reports.security-user-report', compact('users', 'jabatans', 'levels'));
    }

    /**
     * Employee Request Report
     */
    public function requestReport(Request $request)
    {
        $query = EmployeeRequest::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by request type
        if ($request->has('request_type') && $request->request_type) {
            $query->where('request_type', $request->request_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        // Get filter options - using constants from model
        $requestTypes = [
            ['id' => 'shift_change', 'name' => 'Permohonan Tukar Shift'],
            ['id' => 'absence', 'name' => 'Permohonan Tidak Masuk Kerja'],
            ['id' => 'overtime', 'name' => 'Surat Perintah Lembur'],
            ['id' => 'vehicle_asset', 'name' => 'Permintaan Membawa Kendaraan/Inventaris']
        ];

        return view('main.hr.reports.request-report', compact('requests', 'requestTypes'));
    }

    /**
     * Training Report
     */
    public function trainingReport(Request $request)
    {
        $query = TrainingMaster::with(['schedules']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $trainings = $query->orderBy('created_at', 'desc')->get();

        return view('main.hr.reports.training-report', compact('trainings'));
    }

    /**
     * Training Schedule Report
     */
    public function trainingScheduleReport(Request $request)
    {
        $query = TrainingSchedule::with(['training']);

        // Filter by training
        if ($request->has('training_id') && $request->training_id) {
            $query->where('training_id', $request->training_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('schedule_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('schedule_date', '<=', $request->date_to);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->get();

        // Get filter options
        $trainings = TrainingMaster::all();

        return view('main.hr.reports.training-schedule-report', compact('schedules', 'trainings'));
    }

    /**
     * Export User Report to Excel
     */
    public function exportUserReport(Request $request)
    {
        // Implementation for Excel export
        // This would typically use Laravel Excel package
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    /**
     * Export Security User Report to Excel
     */
    public function exportSecurityUserReport(Request $request)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    /**
     * Security Reports Dashboard
     */
    public function securityReports(Request $request)
    {
        // Get date range
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get statistics
        $vehicleChecklists = SecurityVehicleChecklist::whereBetween('tanggal', [$startDate, $endDate]);
        $goodsMovements = SecurityGoodsMovement::whereBetween('tanggal', [$startDate, $endDate]);
        $dailyActivities = SecurityDailyActivityLog::whereBetween('tanggal', [$startDate, $endDate]);

        $stats = [
            'total_vehicle_checklists' => $vehicleChecklists->count(),
            'total_goods_movements' => $goodsMovements->count(),
            'total_daily_activities' => $dailyActivities->count(),
            'today_vehicle_checklists' => SecurityVehicleChecklist::whereDate('tanggal', today())->count(),
            'today_goods_movements' => SecurityGoodsMovement::whereDate('tanggal', today())->count(),
            'today_daily_activities' => SecurityDailyActivityLog::whereDate('tanggal', today())->count(),
        ];

        // Get recent activities
        $recentVehicleChecklists = SecurityVehicleChecklist::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentGoodsMovements = SecurityGoodsMovement::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentDailyActivities = SecurityDailyActivityLog::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('main.hr.reports.security', compact(
            'stats',
            'recentVehicleChecklists',
            'recentGoodsMovements',
            'recentDailyActivities',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Security Vehicle Checklist Report
     */
    public function securityVehicleChecklistReport(Request $request)
    {
        $query = SecurityVehicleChecklist::query();

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver')) {
            $query->where('nama_driver', 'like', '%' . $request->driver . '%');
        }

        $checklists = $query->orderBy('tanggal', 'desc')
                           ->orderBy('no_urut', 'asc')
                           ->get();

        // Get filter options
        $shifts = ['pagi' => 'Pagi (06:00 - 14:00)', 'siang' => 'Siang (14:00 - 22:00)', 'malam' => 'Malam (22:00 - 06:00)'];
        $statuses = ['keluar' => 'Keluar', 'selesai' => 'Selesai'];
        $drivers = SecurityVehicleChecklist::distinct()->pluck('nama_driver')->filter();

        return view('main.hr.reports.vehicle-checklist', compact(
            'checklists',
            'shifts',
            'statuses',
            'drivers'
        ));
    }

    /**
     * Security Goods Movement Report
     */
    public function securityGoodsMovementReport(Request $request)
    {
        $query = SecurityGoodsMovement::query();

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('jenis_movement')) {
            $query->where('jenis_movement', $request->jenis_movement);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('nama_pengunjung')) {
            $query->where('nama_pengunjung', 'like', '%' . $request->nama_pengunjung . '%');
        }

        $movements = $query->orderBy('tanggal', 'desc')
                          ->orderBy('no_urut', 'asc')
                          ->get();

        // Get filter options
        $jenisMovements = ['masuk' => 'Barang Masuk', 'keluar' => 'Barang Keluar'];
        $shifts = ['pagi' => 'Pagi (06:00 - 14:00)', 'siang' => 'Siang (14:00 - 22:00)', 'malam' => 'Malam (22:00 - 06:00)'];
        $pengunjungs = SecurityGoodsMovement::distinct()->pluck('nama_pengunjung')->filter();

        return view('main.hr.reports.goods-movement', compact(
            'movements',
            'jenisMovements',
            'shifts',
            'pengunjungs'
        ));
    }

    /**
     * Security Daily Activity Report
     */
    public function securityDailyActivityReport(Request $request)
    {
        $query = SecurityDailyActivityLog::with('activityEntries');

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('personil_jaga')) {
            $query->where('personil_jaga', 'like', '%' . $request->personil_jaga . '%');
        }

        $activities = $query->orderBy('tanggal', 'desc')
                           ->orderBy('jam_mulai', 'asc')
                           ->get();

        // Get filter options
        $shifts = ['I' => 'Shift I (06:00 - 14:00)', 'II' => 'Shift II (14:00 - 22:00)', 'III' => 'Shift III (22:00 - 06:00)'];
        $personils = SecurityDailyActivityLog::distinct()->pluck('personil_jaga')->filter();

        return view('main.hr.reports.daily-activity', compact(
            'activities',
            'shifts',
            'personils'
        ));
    }

    /**
     * Export Security Vehicle Checklist Report
     */
    public function exportSecurityVehicleChecklist(Request $request)
    {
        $query = SecurityVehicleChecklist::query();

        // Apply same filters as report
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver')) {
            $query->where('nama_driver', 'like', '%' . $request->driver . '%');
        }

        $checklists = $query->orderBy('tanggal', 'desc')
                           ->orderBy('no_urut', 'asc')
                           ->get();

        return view('main.hr.reports.export.vehicle-checklist', compact('checklists'));
    }

    /**
     * Export Security Goods Movement Report
     */
    public function exportSecurityGoodsMovement(Request $request)
    {
        $query = SecurityGoodsMovement::query();

        // Apply same filters as report
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('jenis_movement')) {
            $query->where('jenis_movement', $request->jenis_movement);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('nama_pengunjung')) {
            $query->where('nama_pengunjung', 'like', '%' . $request->nama_pengunjung . '%');
        }

        $movements = $query->orderBy('tanggal', 'desc')
                          ->orderBy('no_urut', 'asc')
                          ->get();

        return view('main.hr.reports.export.goods-movement', compact('movements'));
    }

    /**
     * Export Security Daily Activity Report
     */
    public function exportSecurityDailyActivity(Request $request)
    {
        $query = SecurityDailyActivityLog::with('activityEntries');

        // Apply same filters as report
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('personil_jaga')) {
            $query->where('personil_jaga', 'like', '%' . $request->personil_jaga . '%');
        }

        $activities = $query->orderBy('tanggal', 'desc')
                           ->orderBy('jam_mulai', 'asc')
                           ->get();

        return view('main.hr.reports.export.daily-activity', compact('activities'));
    }
}
