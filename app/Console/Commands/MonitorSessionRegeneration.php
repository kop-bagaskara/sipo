<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class MonitorSessionRegeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:monitor {--hours=24 : Hours to look back} {--action=report : Action to take (report|cleanup|alert)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor excessive session regeneration in Laravel logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $action = $this->option('action');

        $this->info("Monitoring session regeneration for the last {$hours} hours...");

        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            $this->error('Log file not found!');
            return 1;
        }

        $cutoffTime = Carbon::now()->subHours($hours);
        $regenerationPatterns = [
            'HTTPS session established' => 0,
            'Session regenerated for user' => 0,
            'Session regenerated due to CSRF mismatch' => 0,
            'Session token is empty, regenerating session' => 0,
        ];

        $userRegenerations = [];
        $excessiveUsers = [];

        $logContent = File::get($logFile);
        $lines = explode("\n", $logContent);

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            // Parse log line dengan pattern yang lebih fleksibel
            if (preg_match('/\[(.*?)\]/', $line, $timestampMatch)) {
                $timestamp = $timestampMatch[1];

                // Skip jika bukan timestamp yang valid
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)) {
                    continue;
                }

                $logTime = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp);
                if ($logTime->lt($cutoffTime)) continue;

                // Count regeneration patterns
                foreach ($regenerationPatterns as $pattern => &$count) {
                    if (strpos($line, $pattern) !== false) {
                        $count++;

                        // Extract user ID if available
                        if (preg_match('/user:\s*(\d+)/', $line, $userMatch)) {
                            $userId = $userMatch[1];
                            if (!isset($userRegenerations[$userId])) {
                                $userRegenerations[$userId] = 0;
                            }
                            $userRegenerations[$userId]++;
                        }
                    }
                }
            }
        }

        // Generate report
        $this->info("\n=== SESSION REGENERATION REPORT ===");
        $this->info("Period: Last {$hours} hours");
        $this->info("Generated: " . Carbon::now()->format('Y-m-d H:i:s'));

        $this->info("\n--- Regeneration Patterns ---");
        foreach ($regenerationPatterns as $pattern => $count) {
            $this->line("{$pattern}: {$count}");
        }

        $this->info("\n--- User Regeneration Counts ---");
        arsort($userRegenerations);
        foreach ($userRegenerations as $userId => $count) {
            $status = $count > 10 ? '⚠️  HIGH' : ($count > 5 ? '⚠️  MEDIUM' : '✅ NORMAL');
            $this->line("User {$userId}: {$count} regenerations {$status}");

            if ($count > 10) {
                $excessiveUsers[] = $userId;
            }
        }

        // Take action based on option
        switch ($action) {
            case 'cleanup':
                $this->cleanupExcessiveSessions($excessiveUsers);
                break;
            case 'alert':
                $this->sendAlert($excessiveUsers, $regenerationPatterns);
                break;
            default:
                $this->info("\nUse --action=cleanup to force logout excessive users");
                $this->info("Use --action=alert to send notification");
        }

        return 0;
    }

    /**
     * Cleanup excessive sessions
     */
    private function cleanupExcessiveSessions($excessiveUsers)
    {
        if (empty($excessiveUsers)) {
            $this->info("No excessive users found.");
            return;
        }

        $this->warn("Found " . count($excessiveUsers) . " users with excessive session regeneration:");
        foreach ($excessiveUsers as $userId) {
            $this->line("- User ID: {$userId}");
        }

        if ($this->confirm('Do you want to force logout these users?')) {
            // Here you would implement the actual logout logic
            $this->info("Users would be logged out (implementation needed)");
        }
    }

    /**
     * Send alert for excessive regeneration
     */
    private function sendAlert($excessiveUsers, $regenerationPatterns)
    {
        $totalRegenerations = array_sum($regenerationPatterns);

        if ($totalRegenerations > 100 || !empty($excessiveUsers)) {
            $this->warn("⚠️  ALERT: High session regeneration detected!");
            $this->warn("Total regenerations: {$totalRegenerations}");
            $this->warn("Excessive users: " . implode(', ', $excessiveUsers));

            // Here you would implement actual alert logic (email, Slack, etc.)
            $this->info("Alert would be sent (implementation needed)");
        } else {
            $this->info("✅ No alerts needed - session regeneration is normal");
        }
    }
}
