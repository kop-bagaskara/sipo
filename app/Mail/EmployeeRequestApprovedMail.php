<?php

namespace App\Mail;

use App\Models\EmployeeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeRequest;
    public $approver;
    public $requester;
    public $currentLevel;
    public $isFinalApproval;
    public $nextApprovers;

    /**
     * Create a new message instance.
     */
    public function __construct(EmployeeRequest $employeeRequest, User $approver, $currentLevel, $isFinalApproval = false, $nextApprovers = null)
    {
        $this->employeeRequest = $employeeRequest;
        $this->approver = $approver;
        $this->requester = $employeeRequest->employee;
        $this->currentLevel = $currentLevel;
        $this->isFinalApproval = $isFinalApproval;
        $this->nextApprovers = $nextApprovers;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generate email subject based on final approval or intermediate approval
        $subject = $this->isFinalApproval
            ? "🎉 [SIPO] Permohonan Disetujui Semua - {$this->employeeRequest->request_number}"
            : "✅ [SIPO] Permohonan Disetujui - {$this->employeeRequest->request_number}";

        return $this->subject($subject)
                    ->view('emails.employee-request-approved')
                    ->with([
                        'employeeRequest' => $this->employeeRequest,
                        'approver' => $this->approver,
                        'requester' => $this->requester,
                        'currentLevel' => $this->currentLevel,
                        'isFinalApproval' => $this->isFinalApproval,
                        'nextApprovers' => $this->nextApprovers,
                    ]);
    }
}
