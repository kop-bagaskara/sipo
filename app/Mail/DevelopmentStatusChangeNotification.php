<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\JobOrderDevelopment;

class DevelopmentStatusChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $user;
    public $processName;
    public $statusChange;
    public $additionalInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(JobOrderDevelopment $job, $user, $processName, $statusChange, $additionalInfo = [])
    {
        $this->job = $job;
        $this->user = $user;
        $this->processName = $processName;
        $this->statusChange = $statusChange;
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject("Development Job Update - {$this->job->job_code} - {$this->processName}")
                    ->view('emails.development.status-change-notification');
    }
}
