<?php

namespace App\Mail;

use App\Models\EmployeeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRequestPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeRequest;
    public $approver;
    public $requester;
    public $approvalChain;

    /**
     * Create a new message instance.
     */
    public function __construct(EmployeeRequest $employeeRequest, User $approver, $approvalChain = [])
    {
        $this->employeeRequest = $employeeRequest;
        $this->approver = $approver;
        $this->requester = $employeeRequest->employee;
        $this->approvalChain = $approvalChain;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generate email subject based on request type
        $subject = $this->getSubject();

        return $this->subject($subject)
                    ->view('emails.employee-request-pending')
                    ->with([
                        'employeeRequest' => $this->employeeRequest,
                        'approver' => $this->approver,
                        'requester' => $this->requester,
                        'approvalChain' => $this->approvalChain,
                    ]);
    }

    /**
     * Generate email subject based on request type
     */
    private function getSubject()
    {
        $requestNumber = $this->employeeRequest->request_number;
        $requestType = $this->employeeRequest->request_type_label;
        $requesterName = $this->requester->name ?? 'Karyawan';

        return "🔔 [SIPO] Permohonan Baru Menunggu Approval - {$requestNumber} - {$requesterName}";
    }
}
