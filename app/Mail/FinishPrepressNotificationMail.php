<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DevelopmentEmailNotificationSetting;
use App\Models\User;

class FinishPrepressNotificationMail extends Mailable
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
        return $this->subject("Job Prepress Selesai - {$this->jobData['job_code']}")
                    ->view('emails.finish-prepress-notification');
    }
}
