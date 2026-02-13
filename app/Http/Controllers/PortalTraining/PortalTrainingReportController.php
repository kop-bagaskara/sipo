<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingAssignment;
use App\Models\TrainingSessionProgress;
use App\Models\TrainingMaster;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PortalTrainingReportController extends Controller
{
    /**
     * Display training report page
     */
    public function index(Request $request)
    {
        $assignments = collect(); // Empty collection by default
        
        // Only show data if date filters are provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query = TrainingAssignment::with([
                'training',
                'employee',
                'sessionProgress.session'
            ]);

            // Filter by date range (assigned_date)
            $query->whereBetween('assigned_date', [
                $request->date_from,
                $request->date_to . ' 23:59:59'
            ]);

            // Additional filters
            if ($request->filled('training_id')) {
                $query->where('training_id', $request->training_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            $assignments = $query->orderBy('assigned_date', 'desc')->get();
        }

        // Get filter options
        $trainings = TrainingMaster::orderBy('training_name')->get();
        $employees = User::orderBy('name')->get();

        return view('portal-training.master.reports.index', compact('assignments', 'trainings', 'employees'));
    }

    /**
     * Export training report to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = TrainingAssignment::with([
            'training',
            'employee',
            'sessionProgress.session'
        ]);

        // Filter by date range (required)
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('assigned_date', [
                $request->date_from,
                $request->date_to . ' 23:59:59'
            ]);
        } else {
            return redirect()->back()->with('error', 'Tanggal dari dan tanggal sampai harus diisi untuk export.');
        }

        // Additional filters
        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $assignments = $query->orderBy('assigned_date', 'desc')->get();

        $filename = 'Report_Training_Portal_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($assignments) implements FromCollection, WithHeadings, WithMapping {
            private $assignments;

            public function __construct($assignments)
            {
                $this->assignments = $assignments;
            }

            public function collection()
            {
                return $this->assignments;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Tanggal Assign',
                    'Nama Training',
                    'Nama Karyawan',
                    'Status Assignment',
                    'Progress (%)',
                    'Total Sesi',
                    'Sesi Selesai',
                    'Sesi Lulus',
                    'Sesi Gagal',
                    'Total Nilai',
                    'Rata-rata Nilai',
                    'Tanggal Mulai',
                    'Deadline'
                ];
            }

            public function map($assignment): array
            {
                static $no = 1;
                
                $sessionProgresses = $assignment->sessionProgress;
                $totalSessions = $assignment->training->sessions()->active()->count();
                $completedSessions = $sessionProgresses->whereIn('status', [
                    TrainingSessionProgress::STATUS_PASSED,
                    TrainingSessionProgress::STATUS_COMPLETED,
                    TrainingSessionProgress::STATUS_FAILED
                ])->count();
                $passedSessions = $sessionProgresses->where('status', TrainingSessionProgress::STATUS_PASSED)->count();
                $failedSessions = $sessionProgresses->where('status', TrainingSessionProgress::STATUS_FAILED)->count();
                $totalScore = $sessionProgresses->sum('score');
                $averageScore = $sessionProgresses->where('score', '>', 0)->count() > 0 
                    ? $sessionProgresses->where('score', '>', 0)->avg('score') 
                    : 0;

                return [
                    $no++,
                    $assignment->assigned_date ? $assignment->assigned_date->format('d/m/Y') : '-',
                    $assignment->training->training_name ?? '-',
                    $assignment->employee->name ?? '-',
                    ucfirst($assignment->status),
                    number_format($assignment->progress_percentage ?? 0, 2),
                    $totalSessions,
                    $completedSessions,
                    $passedSessions,
                    $failedSessions,
                    number_format($totalScore, 2),
                    number_format($averageScore, 2),
                    $assignment->start_date ? $assignment->start_date->format('d/m/Y') : '-',
                    $assignment->deadline_date ? $assignment->deadline_date->format('d/m/Y') : '-',
                ];
            }
        }, $filename);
    }
}

