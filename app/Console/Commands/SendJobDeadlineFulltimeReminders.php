<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOrderDevelopment;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendJobDeadlineFulltimeReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'development:send-job-deadline-fulltime-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminder emails for job deadline fulltime based on job_deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting job deadline fulltime reminder check...');
        
        try {
            $emailService = new DevelopmentEmailNotificationService();
            
            // Ambil semua job yang memiliki job_deadline
            $jobs = JobOrderDevelopment::whereNotNull('job_deadline')
                ->where('job_deadline', '!=', '')
                ->get();

            $this->info("Total jobs with job_deadline: " . $jobs->count());

            $h10Jobs = collect();
            $h5Jobs = collect();
            $h0Jobs = collect();

            foreach ($jobs as $job) {
                if (!$job->job_deadline) {
                    continue;
                }

                // Hitung deadline job
                $jobDeadline = Carbon::parse($job->job_deadline);
                $now = Carbon::now();
                $daysLeft = $now->diffInDays($jobDeadline, false);

                // Kategorikan berdasarkan sisa hari
                if ($daysLeft == 10) {
                    $h10Jobs->push($job);
                } elseif ($daysLeft == 5) {
                    $h5Jobs->push($job);
                } elseif ($daysLeft == 0) {
                    $h0Jobs->push($job);
                }
            }

            $this->info("Found {$h10Jobs->count()} jobs for H-10 reminder");
            $this->info("Found {$h5Jobs->count()} jobs for H-5 reminder");
            $this->info("Found {$h0Jobs->count()} jobs for H-0 reminder");

            // Kirim reminder H-10
            foreach ($h10Jobs as $job) {
                $this->sendReminderForJob($emailService, $job, '10');
            }

            // Kirim reminder H-5
            foreach ($h5Jobs as $job) {
                $this->sendReminderForJob($emailService, $job, '5');
            }

            // Kirim reminder H-0
            foreach ($h0Jobs as $job) {
                $this->sendReminderForJob($emailService, $job, '0');
            }

            $this->info('Job deadline fulltime reminder check completed successfully.');
            
        } catch (\Exception $e) {
            $this->error('Error in job deadline fulltime reminder check: ' . $e->getMessage());
            Log::error('Error in job deadline fulltime reminder check: ' . $e->getMessage());
        }
    }

    /**
     * Kirim reminder untuk job tertentu
     */
    private function sendReminderForJob($emailService, $job, $reminderType)
    {
        try {
            // Hitung deadline job
            $jobDeadline = Carbon::parse($job->job_deadline);
            $now = Carbon::now();
            $daysLeft = $now->diffInDays($jobDeadline, false);

            // Hitung progress berdasarkan status
            $progressPercentage = $this->calculateProgressPercentage($job->status_job);

            // Siapkan data job untuk email
            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'qty_order_estimation' => (float)$job->qty_order_estimation,
                'job_deadline' => $jobDeadline->format('d/m/Y'),
                'days_left' => $daysLeft,
                'status_job' => $job->status_job,
                'progress_percentage' => $progressPercentage,
                'last_updated' => $job->updated_at->format('d/m/Y H:i')
            ];

            // Kirim notifikasi email
            $emailService->sendJobDeadlineFulltimeNotification($jobData, $reminderType);
            
            $this->info("H-{$reminderType} reminder sent for job: {$job->job_code}");
            
        } catch (\Exception $e) {
            $this->error("Failed to send H-{$reminderType} reminder for job {$job->job_code}: " . $e->getMessage());
            Log::error("Failed to send H-{$reminderType} reminder for job {$job->job_code}: " . $e->getMessage());
        }
    }

    /**
     * Hitung progress percentage berdasarkan status job
     */
    private function calculateProgressPercentage($statusJob)
    {
        $statusProgress = [
            'PENDING' => 0,
            'IN_PROGRESS' => 25,
            'IN_PROGRESS_PREPRESS' => 50,
            'IN_PROGRESS_PRODUCTION' => 75,
            'FINISH_PREPRESS' => 60,
            'FINISH_PRODUCTION' => 90,
            'COMPLETED' => 100,
            'CANCELLED' => 0
        ];

        return $statusProgress[$statusJob] ?? 0;
    }
}
