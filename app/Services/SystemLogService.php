<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemLogService
{
    /**
     * Log perubahan data ke database
     */
    public static function log(
        string $logType,
        string $actionType,
        string $tableName,
        string $recordId,
        string $description,
        array $oldData = null,
        array $newData = null,
        string $recordIdentifier = null,
        Request $request = null
    ) {
        try {
            // Deteksi field yang berubah
            $changedFields = null;
            if ($oldData && $newData) {
                $changedFields = self::detectChangedFields($oldData, $newData);
            }

            // Ambil informasi user
            $user = Auth::user();
            $userId = $user ? $user->id : null;
            $userName = $user ? $user->name : 'System';
            $userJabatan = $user ? $user->jabatan : null;

            // Ambil informasi request
            $ipAddress = $request ? $request->ip() : null;
            $userAgent = $request ? $request->userAgent() : null;

            // Simpan log ke database
            SystemLog::create([
                'log_type' => $logType,
                'action_type' => $actionType,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'record_identifier' => $recordIdentifier,
                'old_data' => $oldData,
                'new_data' => $newData,
                'changed_fields' => $changedFields,
                'description' => $description,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'user_id' => $userId,
                'user_name' => $userName,
                'user_jabatan' => $userJabatan
            ]);

            // Log ke file juga untuk backup
            Log::info("System Log: {$logType} - {$actionType} - {$tableName}:{$recordId} - {$description}", [
                'user_id' => $userId,
                'user_name' => $userName,
                'ip_address' => $ipAddress,
                'changed_fields' => $changedFields
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to save system log: " . $e->getMessage(), [
                'log_type' => $logType,
                'action_type' => $actionType,
                'table_name' => $tableName,
                'record_id' => $recordId
            ]);
        }
    }

    /**
     * Log perubahan status job development
     */
    public static function logJobDevelopmentStatusChange(
        $job,
        string $oldStatus,
        string $newStatus,
        string $actionDescription,
        Request $request = null
    ) {
        self::log(
            'development',
            'status_change',
            'tb_job_order_developments',
            $job->id,
            "Status job development berubah dari '{$oldStatus}' menjadi '{$newStatus}' - {$actionDescription}",
            ['status_job' => $oldStatus],
            ['status_job' => $newStatus],
            $job->job_code,
            $request
        );
    }

    /**
     * Log perubahan data job development
     */
    public static function logJobDevelopmentUpdate(
        $job,
        array $oldData,
        array $newData,
        string $actionDescription,
        Request $request = null
    ) {
        self::log(
            'development',
            'update',
            'tb_job_order_developments',
            $job->id,
            "Data job development diupdate - {$actionDescription}",
            $oldData,
            $newData,
            $job->job_code,
            $request
        );
    }

    /**
     * Log perubahan data job prepress
     */
    public static function logJobPrepressUpdate(
        $prepressJob,
        array $oldData,
        array $newData,
        string $actionDescription,
        Request $request = null
    ) {
        self::log(
            'prepress',
            'update',
            'tb_job_prepresses',
            $prepressJob->id,
            "Data job prepress diupdate - {$actionDescription}",
            $oldData,
            $newData,
            $prepressJob->nomor_job_order,
            $request
        );
    }

    /**
     * Log perubahan status job prepress
     */
    public static function logJobPrepressStatusChange(
        $prepressJob,
        string $oldStatus,
        string $newStatus,
        string $actionDescription,
        Request $request = null
    ) {
        self::log(
            'prepress',
            'status_change',
            'tb_job_prepresses',
            $prepressJob->id,
            "Status job prepress berubah dari '{$oldStatus}' menjadi '{$newStatus}' - {$actionDescription}",
            ['status_job' => $oldStatus],
            ['status_job' => $newStatus],
            $prepressJob->nomor_job_order,
            $request
        );
    }

    /**
     * Log pembuatan data baru
     */
    public static function logCreate(
        string $logType,
        string $tableName,
        string $recordId,
        array $newData,
        string $actionDescription,
        string $recordIdentifier = null,
        Request $request = null
    ) {
        self::log(
            $logType,
            'create',
            $tableName,
            $recordId,
            "Data baru dibuat - {$actionDescription}",
            null,
            $newData,
            $recordIdentifier,
            $request
        );
    }

    /**
     * Log penghapusan data
     */
    public static function logDelete(
        string $logType,
        string $tableName,
        string $recordId,
        array $oldData,
        string $actionDescription,
        string $recordIdentifier = null,
        Request $request = null
    ) {
        self::log(
            $logType,
            'delete',
            $tableName,
            $recordId,
            "Data dihapus - {$actionDescription}",
            $oldData,
            null,
            $recordIdentifier,
            $request
        );
    }

    /**
     * Deteksi field yang berubah
     */
    private static function detectChangedFields(array $oldData, array $newData): string
    {
        $changedFields = [];
        
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changedFields[] = $key;
            }
        }

        return implode(', ', $changedFields);
    }

    /**
     * Get logs dengan filter
     */
    public static function getLogs(array $filters = [], int $perPage = 50)
    {
        $query = SystemLog::query();

        // Filter berdasarkan log type
        if (isset($filters['log_type']) && $filters['log_type']) {
            $query->byLogType($filters['log_type']);
        }

        // Filter berdasarkan action type
        if (isset($filters['action_type']) && $filters['action_type']) {
            $query->byActionType($filters['action_type']);
        }

        // Filter berdasarkan table name
        if (isset($filters['table_name']) && $filters['table_name']) {
            $query->byTableName($filters['table_name']);
        }

        // Filter berdasarkan user
        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->byUser($filters['user_id']);
        }

        // Filter berdasarkan tanggal
        if (isset($filters['start_date']) && $filters['start_date']) {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'] ?? now()->format('Y-m-d');
            $query->byDateRange($startDate, $endDate);
        }

        // Filter berdasarkan record identifier
        if (isset($filters['record_identifier']) && $filters['record_identifier']) {
            $query->byRecordIdentifier($filters['record_identifier']);
        }

        // Search dalam description
        if (isset($filters['search']) && $filters['search']) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get statistics logs
     */
    public static function getLogStatistics()
    {
        $totalLogs = SystemLog::count();
        $todayLogs = SystemLog::whereDate('created_at', today())->count();
        $thisWeekLogs = SystemLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthLogs = SystemLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $logTypes = SystemLog::selectRaw('log_type, COUNT(*) as count')
            ->groupBy('log_type')
            ->pluck('count', 'log_type');

        $actionTypes = SystemLog::selectRaw('action_type, COUNT(*) as count')
            ->groupBy('action_type')
            ->pluck('count', 'action_type');

        $topUsers = SystemLog::selectRaw('user_name, COUNT(*) as count')
            ->whereNotNull('user_name')
            ->groupBy('user_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_logs' => $totalLogs,
            'today_logs' => $todayLogs,
            'this_week_logs' => $thisWeekLogs,
            'this_month_logs' => $thisMonthLogs,
            'log_types' => $logTypes,
            'action_types' => $actionTypes,
            'top_users' => $topUsers
        ];
    }
}
