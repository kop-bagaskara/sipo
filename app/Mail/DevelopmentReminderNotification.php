<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\JobOrderDevelopment;

class DevelopmentReminderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $user;
    public $daysBefore;
    public $processName;
    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct(JobOrderDevelopment $job, $user, $daysBefore, $processName, $reminderType = 'deadline')
    {
        $this->job = $job;
        $this->user = $user;
        $this->daysBefore = $daysBefore;
        $this->processName = $processName;
        $this->reminderType = $reminderType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->daysBefore > 0
            ? "Reminder H-{$this->daysBefore} - {$this->job->job_code} - {$this->processName}"
            : "Deadline Hari Ini - {$this->job->job_code} - {$this->processName}";

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('emails.development.reminder-notification');
    }
}
