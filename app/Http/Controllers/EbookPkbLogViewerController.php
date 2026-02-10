<?php

namespace App\Http\Controllers;

use App\Models\EbookPkbReadingLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EbookPkbLogViewerController extends Controller
{
    /**
     * Display the PKB reading log viewer
     */
    public function index()
    {
        return view('main.ebook-pkb.log-viewer');
    }

    /**
     * Get datatable data for PKB reading logs with user details
     */
    public function getLogData(Request $request)
    {
        $query = EbookPkbReadingLog::with('user')
            ->select([
                'ebook_pkb_reading_logs.*',
            ]);

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchTerm = '%' . $request->search['value'] . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', $searchTerm)
                  ->orWhere('email', 'ilike', $searchTerm)
                  ->orWhere('divisi', 'ilike', $searchTerm)
                  ->orWhere('jabatan', 'ilike', $searchTerm);
            });
        }

        // Sorting
        if ($request->has('order') && count($request->order) > 0) {
            $order = $request->order[0];
            $columnName = $request->columns[$order['column']]['data'];
            $direction = $order['dir'] === 'desc' ? 'desc' : 'asc';

            if ($columnName === 'user_name') {
                $query->orderBy(function($q) {
                    $q->select('name')
                        ->from('users')
                        ->whereColumn('users.id', 'ebook_pkb_reading_logs.user_id');
                }, $direction);
            } elseif ($columnName === 'divisi') {
                $query->orderBy(function($q) {
                    $q->select('divisi')
                        ->from('users')
                        ->whereColumn('users.id', 'ebook_pkb_reading_logs.user_id');
                }, $direction);
            } elseif ($columnName === 'jabatan') {
                $query->orderBy(function($q) {
                    $q->select('jabatan')
                        ->from('users')
                        ->whereColumn('users.id', 'ebook_pkb_reading_logs.user_id');
                }, $direction);
            } else {
                $query->orderBy($columnName, $direction);
            }
        } else {
            $query->orderBy('session_start_at', 'desc');
        }

        // Pagination
        $start = (int)$request->start ?? 0;
        $length = (int)$request->length ?? 10;

        $recordsFiltered = $query->count();
        $logs = $query->skip($start)->take($length)->get();

        // Transform data for datatable
        $data = $logs->map(function($log) {
            $user = $log->user;
            $divisionName = $user ? ($user->divisiUser->nama_divisi ?? $user->divisi ?? '-') : '-';
            $positionName = $user ? ($user->jabatanUser->nama_jabatan ?? $user->jabatan ?? '-') : '-';

            return [
                'id' => $log->id,
                'user_name' => $user ? $user->name : 'Unknown',
                'email' => $user ? $user->email : '-',
                'divisi' => $divisionName,
                'jabatan' => $positionName,
                'start_page' => $log->start_page ?? 1,
                'last_page_viewed' => $log->last_page_viewed ?? 1,
                'total_pages_visited' => $log->total_pages_visited ?? 0,
                'time_spent' => $this->formatDuration($log->time_spent_seconds),
                'time_spent_seconds' => $log->time_spent_seconds ?? 0,
                'session_start_at' => $log->session_start_at ? $log->session_start_at->format('d/m/Y H:i') : '-',
                'session_end_at' => $log->session_end_at ? $log->session_end_at->format('d/m/Y H:i') : 'Ongoing',
                'marked_as_complete' => $log->marked_as_complete ? 'Yes' : 'No',
                'completion_percentage' => $log->last_page_viewed ? round(($log->last_page_viewed / 46) * 100) : 0,
                'pages_visited' => $log->pages_visited ? implode(', ', (array)$log->pages_visited) : '-',
                'interaction_count' => $log->interaction_log ? count((array)$log->interaction_log) : 0,
                'actions' => $log->id
            ];
        });

        return response()->json([
            'draw' => (int)$request->draw,
            'recordsTotal' => EbookPkbReadingLog::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Get detail log for modal view
     */
    public function getLogDetail($id)
    {
        $log = EbookPkbReadingLog::with('user')->findOrFail($id);
        $user = $log->user;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'user' => [
                    'name' => $user->name ?? 'Unknown',
                    'email' => $user->email ?? '-',
                    'divisi' => $user->divisiUser->nama_divisi ?? $user->divisi ?? '-',
                    'jabatan' => $user->jabatanUser->nama_jabatan ?? $user->jabatan ?? '-',
                ],
                'reading_info' => [
                    'start_page' => $log->start_page ?? 1,
                    'last_page_viewed' => $log->last_page_viewed ?? 1,
                    'total_pages_visited' => $log->total_pages_visited ?? 0,
                    'completion_percentage' => $log->last_page_viewed ? round(($log->last_page_viewed / 46) * 100) : 0,
                    'marked_as_complete' => $log->marked_as_complete ? 'Yes' : 'No',
                    'completed_at' => $log->completed_at ? $log->completed_at->format('d/m/Y H:i:s') : '-'
                ],
                'session_info' => [
                    'session_start_at' => $log->session_start_at ? $log->session_start_at->format('d/m/Y H:i:s') : '-',
                    'session_end_at' => $log->session_end_at ? $log->session_end_at->format('d/m/Y H:i:s') : 'Still Reading',
                    'duration' => $this->formatDuration($log->time_spent_seconds),
                    'duration_formatted' => $log->getReadingDuration()
                ],
                'pages_visited' => $log->pages_visited ?? [],
                'interaction_log' => $log->interaction_log ?? [],
                'interaction_count' => $log->interaction_log ? count((array)$log->interaction_log) : 0,
            ]
        ]);
    }

    /**
     * Export logs to Excel
     */
    public function export()
    {
        $logs = EbookPkbReadingLog::with('user')
            ->orderBy('session_start_at', 'desc')
            ->get();

        $fileName = 'PKB_Reading_Logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Nama User',
                'Email',
                'Divisi',
                'Jabatan',
                'Halaman Awal',
                'Halaman Terakhir',
                'Total Halaman Dikunjungi',
                'Waktu Membaca',
                'Mulai Membaca',
                'Akhir Membaca',
                'Status Selesai',
                'Persentase Selesai',
                'Jumlah Interaksi'
            ]);

            // Data rows
            foreach ($logs as $log) {
                $user = $log->user;
                fputcsv($file, [
                    $user->name ?? 'Unknown',
                    $user->email ?? '-',
                    $user->divisiUser->nama_divisi ?? $user->divisi ?? '-',
                    $user->jabatanUser->nama_jabatan ?? $user->jabatan ?? '-',
                    $log->start_page ?? 1,
                    $log->last_page_viewed ?? 1,
                    $log->total_pages_visited ?? 0,
                    $log->getReadingDuration(),
                    $log->session_start_at ? $log->session_start_at->format('d/m/Y H:i:s') : '-',
                    $log->session_end_at ? $log->session_end_at->format('d/m/Y H:i:s') : 'Ongoing',
                    $log->marked_as_complete ? 'Ya' : 'Tidak',
                    $log->last_page_viewed ? round(($log->last_page_viewed / 46) * 100) . '%' : '0%',
                    $log->interaction_log ? count((array)$log->interaction_log) : 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics/summary
     */
    public function getStatistics()
    {
        $totalLogs = EbookPkbReadingLog::count();
        $completedLogs = EbookPkbReadingLog::where('marked_as_complete', true)->count();
        $ongoingLogs = EbookPkbReadingLog::whereNull('session_end_at')->count();
        $totalUsers = EbookPkbReadingLog::distinct('user_id')->count();

        $avgReadingTime = EbookPkbReadingLog::avg('time_spent_seconds') ?? 0;
        $avgPagesRead = EbookPkbReadingLog::avg('total_pages_visited') ?? 0;

        // Top 5 most active users
        $topUsers = EbookPkbReadingLog::with('user')
            ->groupBy('user_id')
            ->select('user_id', DB::raw('COUNT(*) as session_count'), DB::raw('SUM(time_spent_seconds) as total_time'))
            ->orderBy('total_time', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_logs' => $totalLogs,
                'completed_logs' => $completedLogs,
                'completion_rate' => $totalLogs > 0 ? round(($completedLogs / $totalLogs) * 100, 2) . '%' : '0%',
                'ongoing_logs' => $ongoingLogs,
                'total_unique_users' => $totalUsers,
                'avg_reading_time' => $this->formatDuration($avgReadingTime),
                'avg_pages_read' => round($avgPagesRead, 2),
            ],
            'top_users' => $topUsers->map(function($log) {
                return [
                    'name' => $log->user->name ?? 'Unknown',
                    'email' => $log->user->email ?? '-',
                    'divisi' => $log->user->divisiUser->nama_divisi ?? $log->user->divisi ?? '-',
                    'session_count' => $log->session_count,
                    'total_time' => $this->formatDuration($log->total_time)
                ];
            })
        ]);
    }

    /**
     * Format duration in seconds to readable format
     */
    private function formatDuration($seconds)
    {
        if (!$seconds || $seconds < 0) {
            return '0m';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($minutes > 0) {
            return "{$minutes}m {$secs}s";
        } else {
            return "{$secs}s";
        }
    }
}
