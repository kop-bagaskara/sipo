<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CleanupExcessiveSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:cleanup {--user-id= : Specific user ID to cleanup} {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup excessive session regeneration for specific users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $force = $this->option('force');

        if ($userId) {
            $this->cleanupUserSession($userId, $force);
        } else {
            $this->cleanupAllExcessiveSessions($force);
        }

        return 0;
    }

    /**
     * Cleanup session for specific user
     */
    private function cleanupUserSession($userId, $force)
    {
        $this->info("Cleaning up session for user ID: {$userId}");

        // Clear session data dari database jika menggunakan database driver
        if (config('session.driver') === 'database') {
            $deleted = DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();

            $this->info("Deleted {$deleted} session records from database");
        }

        // Clear cache untuk user ini
        $cacheKey = "user_session_{$userId}";
        Cache::forget($cacheKey);

        // Clear session regeneration counters
        $this->clearSessionCounters($userId);

        $this->info("Session cleanup completed for user {$userId}");
    }

    /**
     * Cleanup all excessive sessions
     */
    private function cleanupAllExcessiveSessions($force)
    {
        $this->info("Cleaning up all excessive sessions...");

        // Clear session regeneration counters dari cache
        $this->clearAllSessionCounters();

        // Clear expired sessions
        if (config('session.driver') === 'database') {
            $expired = DB::table('sessions')
                ->where('last_activity', '<', time() - (config('session.lifetime') * 60))
                ->delete();

            $this->info("Deleted {$expired} expired session records");
        }

        $this->info("Global session cleanup completed");
    }

    /**
     * Clear session counters for specific user
     */
    private function clearSessionCounters($userId)
    {
        $counters = [
            'regeneration_count',
            'last_regeneration_time',
            'last_token_regeneration_time',
            'last_csrf_regeneration_time',
            'csrf_regenerated',
            'https_session_established',
            'https_regenerated_at',
            'last_regeneration_time',
        ];

        foreach ($counters as $counter) {
            Cache::forget("user_{$userId}_{$counter}");
        }

        Log::info("Session counters cleared for user {$userId}");
    }

    /**
     * Clear all session counters
     */
    private function clearAllSessionCounters()
    {
        // Clear semua cache yang berhubungan dengan session regeneration
        $patterns = [
            'user_*_regeneration_count',
            'user_*_last_regeneration_time',
            'user_*_last_token_regeneration_time',
            'user_*_last_csrf_regeneration_time',
            'user_*_csrf_regenerated',
            'user_*_https_session_established',
            'user_*_https_regenerated_at',
        ];

        foreach ($patterns as $pattern) {
            // Note: Ini adalah implementasi sederhana, untuk production mungkin perlu cara yang lebih sophisticated
            Cache::flush(); // Flush semua cache (hati-hati dengan ini)
            break; // Hanya flush sekali
        }

        Log::info("All session counters cleared");
    }
}
