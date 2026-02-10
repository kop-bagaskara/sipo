<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobPrepress;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;

class SendPicPrepressReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:send-pic-prepress-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for PIC Prepress (H-3, H-2, H-1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting PIC Prepress reminder check...');

        $emailService = new DevelopmentEmailNotificationService();
        $today = Carbon::today();
        $h3Date = $today->copy()->addDays(3);
        $h2Date = $today->copy()->addDays(2);
        $h1Date = $today->copy()->addDays(1);

        // Get all prepress jobs that are not CLOSED or APPROVED
        // Join dengan tb_assign_job_prepresses untuk mendapatkan PIC yang bertanggung jawab
        // Filter khusus untuk job yang berawalan "DEV-"
        $prepressJobs = JobPrepress::join('tb_assign_job_prepresses', 'tb_job_prepresses.id', '=', 'tb_assign_job_prepresses.id_job_order')
            ->whereNotIn('tb_job_prepresses.status_job', ['CLOSED', 'APPROVED'])
            ->whereNotNull('tb_job_prepresses.tanggal_deadline')
            ->where('tb_job_prepresses.nomor_job_order', 'LIKE', 'DEV-%')
            ->select('tb_job_prepresses.*', 'tb_assign_job_prepresses.id_user_pic')
            ->get();

        $h3Jobs = collect();
        $h2Jobs = collect();
        $h1Jobs = collect();

        foreach ($prepressJobs as $job) {
            $deadline = Carbon::parse($job->tanggal_deadline);

            // Check H-3 (3 days before deadline)
            if ($deadline->format('Y-m-d') === $h3Date->format('Y-m-d')) {
                $h3Jobs->push($job);
            }

            // Check H-2 (2 days before deadline)
            if ($deadline->format('Y-m-d') === $h2Date->format('Y-m-d')) {
                $h2Jobs->push($job);
            }

            // Check H-1 (1 day before deadline)
            if ($deadline->format('Y-m-d') === $h1Date->format('Y-m-d')) {
                $h1Jobs->push($job);
            }
        }

        $this->info("Total DEV- jobs found: {$prepressJobs->count()}");
        $this->info("Found {$h3Jobs->count()} DEV- jobs for H-3 reminder");
        $this->info("Found {$h2Jobs->count()} DEV- jobs for H-2 reminder");
        $this->info("Found {$h1Jobs->count()} DEV- jobs for H-1 reminder");

        // Send H-3 reminders
        foreach ($h3Jobs as $job) {
            $this->sendReminderForJob($emailService, $job, 'H-3');
        }

        // Send H-2 reminders
        foreach ($h2Jobs as $job) {
            $this->sendReminderForJob($emailService, $job, 'H-2');
        }

        // Send H-1 reminders
        foreach ($h1Jobs as $job) {
            $this->sendReminderForJob($emailService, $job, 'H-1');
        }

        $this->info('PIC Prepress reminder check completed!');
    }

    /**
     * Send reminder for a specific job
     */
    private function sendReminderForJob($emailService, $job, $reminderType)
    {
        try {
            $jobData = [
                'id' => $job->id,
                'job_code' => $job->nomor_job_order ?? 'N/A',
                'job_name' => $job->product ?? 'N/A',
                'customer' => $job->customer ?? 'N/A',
                'product' => $job->product ?? 'N/A',
                'qty_order_estimation' => $job->qty ?? 0,
                'prioritas_job' => $job->prioritas_job ?? 'Normal',
                'prepress_deadline' => $job->tanggal_deadline,
                'catatan' => $job->catatan ?? '',
                'assigned_pic_id' => $job->id_user_pic ?? null
            ];

            $success = $emailService->sendPicPrepressReminderNotification($jobData, $reminderType);

            if ($success) {
                $this->info("✓ {$reminderType} reminder sent for DEV- job: {$job->nomor_job_order} (Deadline: {$job->tanggal_deadline})");
            } else {
                $this->error("✗ Failed to send {$reminderType} reminder for DEV- job: {$job->nomor_job_order}");
            }
        } catch (\Exception $e) {
            $this->error("✗ Error sending {$reminderType} reminder for job {$job->nomor_job_order}: " . $e->getMessage());
        }
    }
}
