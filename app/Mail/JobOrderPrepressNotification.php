<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobOrderPrepressNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $jobOrder;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($jobOrder, $user)
    {
        $this->jobOrder = $jobOrder;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Job Order Prepress Baru - ' . $this->jobOrder->kode_design)
                    ->view('emails.job-order-prepress-notification');
    }
}
