<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DevelopmentEmailNotificationSetting;
use App\Models\User;

class PicPrepressReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $setting;
    public $reminder;
    public $jobData;
    public $additionalData;
    public $currentUser;

    /**
     * Create a new message instance.
     */
    public function __construct(
        DevelopmentEmailNotificationSetting $setting,
        array $reminder,
        array $jobData,
        array $additionalData,
        User $currentUser
    ) {
        $this->setting = $setting;
        $this->reminder = $reminder;
        $this->jobData = $jobData;
        $this->additionalData = $additionalData;
        $this->currentUser = $currentUser;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $reminderType = $this->additionalData['reminder_type'] ?? 'Reminder';
        $subject = "{$reminderType} - PIC Prepress - {$this->jobData['job_code']}";

        // Siapkan data untuk template reminder-notification
        $user = $this->currentUser;
        $job = (object) [
            'id' => $this->jobData['id'],
            'job_code' => $this->jobData['job_code'],
            'customer' => $this->jobData['customer'],
            'product' => $this->jobData['product'],
            'status_job' => $this->jobData['status_job']
        ];

        // dd($job);
        
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

        return $this->subject($subject)
                    ->view('emails.development.reminder-notification')
                    ->with([
                        'user' => $user,
                        'job' => $job,
                        'processName' => $processName,
                        'daysBefore' => $daysBefore
                    ]);
    }
}
