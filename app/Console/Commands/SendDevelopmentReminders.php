<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOrderDevelopment;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;

class SendDevelopmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for development jobs based on deadlines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting development reminder process...');

        $emailService = new DevelopmentEmailNotificationService();

        // Get all active development jobs
        $jobs = JobOrderDevelopment::whereNotNull('job_deadline')
            ->whereNotIn('status_job', ['COMPLETED', 'CANCELLED'])
            ->get();

        $this->info("Found {$jobs->count()} jobs to check for reminders");

        foreach ($jobs as $job) {
            try {
                $emailService->checkAndSendReminders($job);
                $this->line("Processed reminders for job: {$job->job_code}");
            } catch (\Exception $e) {
                $this->error("Error processing job {$job->job_code}: " . $e->getMessage());
            }
        }

        $this->info('Development reminder process completed!');
    }
}
