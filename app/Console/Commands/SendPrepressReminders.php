<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOrderDevelopment;
use App\Models\JobPrepress;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;

class SendPrepressReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:send-prepress-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for prepress jobs (H-2 and H-1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting prepress reminder check...');

        $emailService = new DevelopmentEmailNotificationService();
        $today = Carbon::today();
        $h2Date = $today->copy()->addDays(2);
        // dd($h2Date);
        $h1Date = $today->copy()->addDays(1);

        // Get all development jobs with IN_PROGRESS_PREPRESS status
        $developmentJobs = JobOrderDevelopment::where('status_job', 'IN_PROGRESS_PREPRESS')->get();

        // dd($developmentJobs);
        $h2Jobs = collect();
        $h1Jobs = collect();

        foreach ($developmentJobs as $devJob) {
            $prepressJobs = JobPrepress::where('nomor_job_order', 'LIKE', $devJob->job_code . '%')->get();

            foreach ($prepressJobs as $prepressJob) {
                $deadline = Carbon::parse($prepressJob->tanggal_deadline);
                // dd($deadline);

                // dd($deadline->format('Y-m-d'), $h2Date->format('Y-m-d'), $h1Date->format('Y-m-d'));

                if ($deadline->format('Y-m-d') === $h2Date->format('Y-m-d')) {
                    // dd('h2');
                    $h2Jobs->push($devJob);
                    break;
                }

                if ($deadline->format('Y-m-d') === $h1Date->format('Y-m-d')) {
                    // dd('h1');
                    $h1Jobs->push($devJob);
                    break;
                }
            }
        }

        $this->info("Found {$h2Jobs->count()} jobs for H-2 reminder");
        $this->info("Found {$h1Jobs->count()} jobs for H-1 reminder");

        // Send H-2 reminders (multiple jobs in one email)
        if ($h2Jobs->count() > 0) {
            $this->sendReminderForMultipleJobs($emailService, $h2Jobs, 'H-2');
        }

        // Send H-1 reminders (multiple jobs in one email)
        if ($h1Jobs->count() > 0) {
            $this->sendReminderForMultipleJobs($emailService, $h1Jobs, 'H-1');
        }

        $this->info('Prepress reminder check completed!');
    }

    /**
     * Send reminder for multiple jobs in one email
     */
    private function sendReminderForMultipleJobs($emailService, $jobs, $reminderType)
    {
        try {
            // Convert jobs to array format for email
            $jobsData = [];
            foreach ($jobs as $job) {
                // Find prepress job to get actual deadline
                $prepressJob = JobPrepress::where('nomor_job_order', 'LIKE', $job->job_code . '%')->first();
                $prepressDeadline = $prepressJob ? Carbon::parse($prepressJob->tanggal_deadline) : Carbon::parse($job->tanggal)->addDays(3);

                $jobsData[] = (object) [
                    'id' => $job->id,
                    'job_code' => $job->job_code,
                    'customer' => $job->customer,
                    'product' => $job->product,
                    'status_job' => $job->status_job
                ];
            }

            $success = $emailService->sendPrepressReminderNotificationMultiple($jobsData, $reminderType);

            if ($success) {
                $this->info("✓ {$reminderType} reminder sent for {$jobs->count()} jobs");
            } else {
                $this->error("✗ Failed to send {$reminderType} reminder for {$jobs->count()} jobs");
            }
        } catch (\Exception $e) {
            $this->error("✗ Error sending {$reminderType} reminder for {$jobs->count()} jobs: " . $e->getMessage());
        }
    }

    /**
     * Send reminder for a specific job
     */
    private function sendReminderForJob($emailService, $job, $reminderType)
    {
        // dd($job);
        try {
            // Find prepress job to get actual deadline
            $prepressJob = JobPrepress::where('nomor_job_order', 'LIKE', $job->job_code . '%')->first();
            $prepressDeadline = $prepressJob ? Carbon::parse($prepressJob->tanggal_deadline) : Carbon::parse($job->tanggal)->addDays(3);

            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design,
                'dimension' => $job->dimension,
                'material' => $job->material,
                'total_color' => $job->total_color,
                'colors' => $job->colors,
                'qty_order_estimation' => $job->qty_order_estimation,
                'job_type' => $job->job_type,
                'prioritas_job' => $job->prioritas_job,
                'tanggal' => $job->tanggal,
                'prepress_deadline' => $prepressDeadline,
                'catatan' => $job->catatan,
                'job_order' => $job->job_order,
                'status_job' => $job->status_job
            ];

            $success = $emailService->sendPrepressReminderNotification($jobData, $reminderType);

            if ($success) {
                $this->info("✓ {$reminderType} reminder sent for job: {$job->job_code} (Deadline: {$prepressDeadline->format('Y-m-d')})");
            } else {
                $this->error("✗ Failed to send {$reminderType} reminder for job: {$job->job_code}");
            }
        } catch (\Exception $e) {
            $this->error("✗ Error sending {$reminderType} reminder for job {$job->job_code}: " . $e->getMessage());
        }
    }
}
