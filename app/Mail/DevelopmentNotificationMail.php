<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DevelopmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $setting = $this->emailData['setting'];
        $reminder = $this->emailData['reminder'];
        $jobData = $this->emailData['jobData'];
        $additionalData = $this->emailData['additionalData'];

        // Tentukan subject email
        $subject = $this->getEmailSubject($setting, $reminder, $jobData);

        return $this->subject($subject)
                    ->view('emails.development-notification')
                    ->with([
                        'setting' => $setting,
                        'reminder' => $reminder,
                        'jobData' => $jobData,
                        'additionalData' => $additionalData,
                        'currentUser' => auth()->user()
                    ]);
    }

    /**
     * Generate email subject based on reminder and job data
     */
    private function getEmailSubject($setting, $reminder, $jobData)
    {
        $days = $reminder['days'] ?? 'N/A';
        $customer = $jobData['customer'] ?? 'Unknown';
        $product = $jobData['product'] ?? 'Unknown';

        if ($days === 'first') {
            return "🚀 [SIPO] Job Development Baru - {$customer} - {$product}";
        } elseif (is_numeric($days)) {
            return "⏰ [SIPO] Reminder H-{$days} - {$customer} - {$product}";
        } else {
            return "📧 [SIPO] Notifikasi Development - {$customer} - {$product}";
        }
    }
}
