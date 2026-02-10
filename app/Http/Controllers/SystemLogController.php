<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\User;
use App\Services\SystemLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemLogController extends Controller
{
    /**
     * Display system logs dengan filter
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [
            'log_type' => $request->get('log_type'),
            'action_type' => $request->get('action_type'),
            'table_name' => $request->get('table_name'),
            'user_id' => $request->get('user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'record_identifier' => $request->get('record_identifier'),
            'search' => $request->get('search')
        ];

        // Get logs dengan filter
        $logs = SystemLogService::getLogs($filters, 50);

        // Get filter options
        $logTypes = SystemLog::select('log_type')->distinct()->pluck('log_type');
        $actionTypes = SystemLog::select('action_type')->distinct()->pluck('action_type');
        $tableNames = SystemLog::select('table_name')->distinct()->pluck('table_name');
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Get statistics
        $statistics = SystemLogService::getLogStatistics();

        return view('admin.system-logs.index', compact('logs', 'filters', 'logTypes', 'actionTypes', 'tableNames', 'users', 'statistics'));
    }

    /**
     * Show detail log
     */
    public function show($id)
    {
        $log = SystemLog::findOrFail($id);
        
        return view('admin.system-logs.show', compact('log'));
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $filters = [
            'log_type' => $request->get('log_type'),
            'action_type' => $request->get('action_type'),
            'table_name' => $request->get('table_name'),
            'user_id' => $request->get('user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'record_identifier' => $request->get('record_identifier'),
            'search' => $request->get('search')
        ];

        $logs = SystemLogService::getLogs($filters, 10000); // Get more records for export

        $filename = 'system_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'ID',
                'Tanggal',
                'Log Type',
                'Action Type',
                'Table Name',
                'Record ID',
                'Record Identifier',
                'Description',
                'Changed Fields',
                'User',
                'Jabatan',
                'IP Address'
            ]);

            // CSV Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->log_type,
                    $log->action_type,
                    $log->table_name,
                    $log->record_id,
                    $log->record_identifier,
                    $log->description,
                    $log->changed_fields,
                    $log->user_name,
                    $log->user_jabatan,
                    $log->ip_address
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get logs statistics untuk dashboard
     */
    public function statistics()
    {
        $statistics = SystemLogService::getLogStatistics();
        
        return response()->json($statistics);
    }

    /**
     * Get recent logs untuk dashboard
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $logs = SystemLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($logs);
    }

    /**
     * Get logs by record identifier
     */
    public function byRecord(Request $request, $identifier)
    {
        $logs = SystemLog::where('record_identifier', $identifier)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.system-logs.by-record', compact('logs', 'identifier'));
    }

    /**
     * Get logs by user
     */
    public function byUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $logs = SystemLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.system-logs.by-user', compact('logs', 'user'));
    }

    /**
     * Clean old logs (untuk maintenance)
     */
    public function clean(Request $request)
    {
        $days = $request->get('days', 90); // Default 90 hari
        
        $deletedCount = SystemLog::where('created_at', '<', now()->subDays($days))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus {$deletedCount} log yang lebih dari {$days} hari",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get log types untuk filter dropdown
     */
    public function getLogTypes()
    {
        $logTypes = SystemLog::select('log_type')
            ->distinct()
            ->orderBy('log_type')
            ->pluck('log_type');

        return response()->json($logTypes);
    }

    /**
     * Get action types untuk filter dropdown
     */
    public function getActionTypes()
    {
        $actionTypes = SystemLog::select('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->pluck('action_type');

        return response()->json($actionTypes);
    }

    /**
     * Get table names untuk filter dropdown
     */
    public function getTableNames()
    {
        $tableNames = SystemLog::select('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');

        return response()->json($tableNames);
    }
}
