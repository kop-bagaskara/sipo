<?php

namespace App\Mail;

use App\Models\EmployeeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRequestRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeRequest;
    public $approver;
    public $requester;
    public $currentLevel;
    public $rejectionReason;

    /**
     * Create a new message instance.
     */
    public function __construct(EmployeeRequest $employeeRequest, User $approver, $currentLevel, $rejectionReason = null)
    {
        $this->employeeRequest = $employeeRequest;
        $this->approver = $approver;
        $this->requester = $employeeRequest->employee;
        $this->currentLevel = $currentLevel;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "❌ [SIPO] Permohonan Ditolak - {$this->employeeRequest->request_number}";

        return $this->subject($subject)
                    ->view('emails.employee-request-rejected')
                    ->with([
                        'employeeRequest' => $this->employeeRequest,
                        'approver' => $this->approver,
                        'requester' => $this->requester,
                        'currentLevel' => $this->currentLevel,
                        'rejectionReason' => $this->rejectionReason,
                    ]);
    }
}
