<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\JobOrderDevelopment;

class MeetingOPPNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $user;
    public $meetingData;
    public $meetingNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(JobOrderDevelopment $job, $user, $meetingData, $meetingNumber)
    {
        $this->job = $job;
        $this->user = $user;
        $this->meetingData = $meetingData;
        $this->meetingNumber = $meetingNumber;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject("Meeting OPP {$this->meetingNumber} - {$this->job->job_code} - {$this->meetingData['status']}")
                    ->view('emails.development.meeting-opp-notification');
    }
}
