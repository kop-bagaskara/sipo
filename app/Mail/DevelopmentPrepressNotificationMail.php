<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DevelopmentPrepressNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $setting;
    public $reminder;
    public $jobData;
    public $jobs; // Untuk multiple jobs
    public $additionalData;
    public $currentUser;

    /**
     * Create a new message instance.
     */
    public function __construct($setting, $reminder, $jobData, $additionalData, $currentUser = null, $jobs = null)
    {
        $this->setting = $setting;
        $this->reminder = $reminder;
        $this->jobData = $jobData;
        $this->jobs = $jobs; // Multiple jobs
        $this->additionalData = $additionalData;
        $this->currentUser = $currentUser;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->getSubject();

        // Siapkan data untuk template reminder-notification
        $user = $this->currentUser;
        
        // Jika ada multiple jobs, gunakan itu, jika tidak gunakan single job
        if ($this->jobs && is_array($this->jobs)) {
            $jobs = $this->jobs;
        } else {
            // Handle both array and object data
            $jobData = is_array($this->jobData) ? $this->jobData : (array) $this->jobData;
            $job = (object) [
                'id' => $jobData['id'] ?? 0,
                'job_code' => $jobData['job_code'] ?? '',
                'customer' => $jobData['customer'] ?? '',
                'product' => $jobData['product'] ?? '',
                'status_job' => $jobData['status_job'] ?? ''
            ];
        }
        
        $processName = 'Prepress';
        
        // Hitung days before berdasarkan reminder type
        $daysBefore = 3; // default
        if (isset($this->additionalData['reminder_type'])) {
            $reminderType = $this->additionalData['reminder_type'];
            if ($reminderType === 'H-1') {
                $daysBefore = 1;
            } elseif ($reminderType === 'H-2') {
                $daysBefore = 2;
            } elseif ($reminderType === 'H-3') {
                $daysBefore = 3;
            }
        }

        return $this->view('emails.development.reminder-notification')
                    ->subject($subject)
                    ->with([
                        'user' => $user,
                        'job' => $job ?? null,
                        'jobs' => $jobs ?? null,
                        'processName' => $processName,
                        'daysBefore' => $daysBefore
                    ]);
    }

    /**
     * Get email subject based on notification type
     */
    private function getSubject()
    {
        // Handle both array and object formats
        if (is_array($this->jobData)) {
            $jobCode = $this->jobData['job_code'] ?? 'N/A';
            $customer = $this->jobData['customer'] ?? 'N/A';
            $product = $this->jobData['product'] ?? 'N/A';
        } else {
            $jobCode = $this->jobData->job_code ?? 'N/A';
            $customer = $this->jobData->customer ?? 'N/A';
            $product = $this->jobData->product ?? 'N/A';
        }

        if ($this->additionalData['notification_type'] === 'prepress') {
            return "🖨️ [SIPO] Job Prepress Baru";
        } elseif ($this->additionalData['notification_type'] === 'prepress_reminder') {
            $reminderType = $this->additionalData['reminder_type'] ?? 'H-2';
            return "⏰ [SIPO] Notifikasi Development - REMINDER JOB DEVELOPMENT";
        }

        return "📧 [SIPO] Notifikasi Development - REMINDER JOB DEVELOPMENT";
    }
}
