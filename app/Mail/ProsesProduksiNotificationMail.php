<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DevelopmentEmailNotificationSetting;

class ProsesProduksiNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $setting;
    public $reminder;
    public $jobData;
    public $additionalData;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(
        DevelopmentEmailNotificationSetting $setting,
        array $reminder,
        array $jobData,
        array $additionalData,
        $recipient = null
    ) {
        $this->setting = $setting;
        $this->reminder = $reminder;
        $this->jobData = $jobData;
        $this->additionalData = $additionalData;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.proses-produksi-notification')
                    ->subject($this->setting->process_name . ' - ' . $this->reminder['description'] . ' - ' . $this->jobData['job_code'])
                    ->with([
                        'setting' => $this->setting,
                        'reminder' => $this->reminder,
                        'jobData' => $this->jobData,
                        'additionalData' => $this->additionalData,
                        'currentUser' => $this->recipient
                    ]);
    }
}
