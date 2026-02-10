<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOrderDevelopment;
use App\Models\LeadTimeConfiguration;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendProsesProduksiReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'development:send-proses-produksi-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminder emails for proses produksi (H-4, H-2, H) based on database configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting proses produksi reminder check...');
        
        try {
            $emailService = new DevelopmentEmailNotificationService();
            $today = Carbon::today();
            
            // Get jobs with lead time configuration
            $developmentJobs = JobOrderDevelopment::whereHas('leadTimeConfiguration')
                ->with(['leadTimeConfiguration'])
                ->whereIn('status_job', ['SCHEDULED_FOR_PRODUCTION'])
                ->get();

            // dd($developmentJobs);

            $this->info("Found {$developmentJobs->count()} jobs to check for proses produksi reminders");

            $h4Jobs = collect();
            $h2Jobs = collect();
            $h0Jobs = collect();

            foreach ($developmentJobs as $devJob) {
                $leadTime = $devJob->leadTimeConfiguration;
                
                if (!$leadTime || !$leadTime->total_lead_time_days) {
                    $this->line("Job {$devJob->job_code}: No lead time configuration");
                    continue;
                }

                // Hitung deadline produksi berdasarkan created_at + total_lead_time_days
                $leadTimeStartedAt = Carbon::parse($leadTime->created_at);
                $productionDeadline = $leadTimeStartedAt->copy()->addDays($leadTime->total_lead_time_days);
                $daysToDeadline = $today->diffInDays($productionDeadline, false);

                $this->line("Job {$devJob->job_code}: Started {$leadTimeStartedAt->format('Y-m-d')}, Lead time {$leadTime->total_lead_time_days} days, Deadline {$productionDeadline->format('Y-m-d')}, Days to deadline: {$daysToDeadline}");

                // H-4: 4 hari sebelum deadline
                if ($daysToDeadline == 4) {
                    $h4Jobs->push($devJob);
                    $this->line("  → Added to H-4 reminder");
                }
                // H-2: 2 hari sebelum deadline  
                elseif ($daysToDeadline == 2) {
                    $h2Jobs->push($devJob);
                    $this->line("  → Added to H-2 reminder");
                }
                // H: Hari H (deadline hari ini atau sudah lewat)
                elseif ($daysToDeadline <= 0) {
                    $h0Jobs->push($devJob);
                    $this->line("  → Added to H reminder");
                }
            }

            $this->info("Found {$h4Jobs->count()} jobs for H-4 reminder");
            $this->info("Found {$h2Jobs->count()} jobs for H-2 reminder");
            $this->info("Found {$h0Jobs->count()} jobs for H reminder");

            // Send H-4 reminders (PIC PPIC only)
            if ($h4Jobs->count() > 0) {
                $this->sendReminderForMultipleJobs($emailService, $h4Jobs, 'H-4');
            }

            // Send H-2 reminders (PIC & SCM Head)
            if ($h2Jobs->count() > 0) {
                $this->sendReminderForMultipleJobs($emailService, $h2Jobs, 'H-2');
            }

            // Send H reminders (PIC, SCM Head, SCM Mgr)
            if ($h0Jobs->count() > 0) {
                $this->sendReminderForMultipleJobs($emailService, $h0Jobs, 'H');
            }

            $this->info('Proses produksi reminder check completed successfully.');
            
        } catch (\Exception $e) {
            $this->error('Error in proses produksi reminder check: ' . $e->getMessage());
            Log::error('Error in proses produksi reminder check: ' . $e->getMessage());
        }
    }

    /**
     * Send reminder for multiple jobs in one email (sama seperti prepress)
     */
    private function sendReminderForMultipleJobs($emailService, $jobs, $reminderType)
    {
        try {
            // Convert jobs to array format for email
            $jobsData = [];
            foreach ($jobs as $job) {
                $leadTime = $job->leadTimeConfiguration;
                $leadTimeStartedAt = Carbon::parse($leadTime->created_at);
                $productionDeadline = $leadTimeStartedAt->copy()->addDays($leadTime->total_lead_time_days);
                
                $jobsData[] = (object) [
                    'id' => $job->id,
                    'job_code' => $job->job_code,
                    'customer' => $job->customer,
                    'product' => $job->product,
                    'status_job' => $job->status_job,
                    'production_deadline' => $productionDeadline->format('Y-m-d'),
                    'lead_time_started_at' => $leadTimeStartedAt->format('Y-m-d'),
                    'total_lead_time_days' => $leadTime->total_lead_time_days,
                    'days_before' => $this->getDaysBefore($reminderType)
                ];
            }

            $success = $emailService->sendProsesProduksiReminderNotificationMultiple($jobsData, $reminderType);

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
     * Get days before based on reminder type
     */
    private function getDaysBefore($reminderType)
    {
        switch ($reminderType) {
            case 'H-4':
                return 4;
            case 'H-2':
                return 2;
            case 'H':
                return 0;
            default:
                return 0;
        }
    }
}
