<?php

namespace App\Services;

use App\Models\EmployeeRequest;
use App\Models\User;
use App\Models\EmailNotificationSetting;
use App\Mail\EmployeeRequestPendingMail;
use App\Mail\EmployeeRequestApprovedMail;
use App\Mail\EmployeeRequestRejectedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmployeeRequestNotificationService
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Kirim notifikasi email ke approver pertama ketika request baru dibuat
     */
    public function notifyFirstApprovers(EmployeeRequest $employeeRequest)
    {
        try {
            // Cek apakah email notification aktif untuk kirim_permohonan_hr
            if (!EmailNotificationSetting::isActive('kirim_permohonan_hr')) {
                Log::info('Email notification is disabled for kirim_permohonan_hr', [
                    'request_id' => $employeeRequest->id,
                    'request_number' => $employeeRequest->request_number,
                ]);
                return;
            }

            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name');

            Log::info('========== EMAIL NOTIFICATION START ==========', [
                'type' => 'FIRST_APPROVER_NOTIFICATION',
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'request_type' => $employeeRequest->request_type,
                'employee_name' => $employeeRequest->employee ? $employeeRequest->employee->name : 'Unknown',
                'employee_email' => $employeeRequest->employee ? $employeeRequest->employee->email : 'Unknown',
            ]);

            // Get approval chain
            $chain = $this->approvalService->getApprovalChain($employeeRequest);

            if (empty($chain)) {
                Log::warning('No approval chain found', [
                    'request_id' => $employeeRequest->id,
                ]);
                return;
            }

            // Get first approvers (order_1)
            $firstLevel = reset($chain);

            if (!$firstLevel) {
                Log::warning('No first level found in approval chain', [
                    'request_id' => $employeeRequest->id,
                ]);
                return;
            }

            $approvers = $firstLevel['users'] ?? collect();

            if ($approvers->isEmpty()) {
                Log::warning('No approvers found for first level', [
                    'request_id' => $employeeRequest->id,
                ]);
                return;
            }

            // Kirim email ke semua approvers di level pertama
            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($approvers as $approver) {
                $toEmail = $approver->email;
                $toName = $approver->name;
                $subject = "🔔 [SIPO] Permohonan Baru Menunggu Approval - {$employeeRequest->request_number} - {$employeeRequest->employee->name}";

                Log::info('--- Sending Email to Approver ---', [
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'from_email' => $fromAddress,
                    'from_name' => $fromName,
                    'subject' => $subject,
                ]);

                if ($toEmail) {
                    try {
                        Mail::to($toEmail)->send(new EmployeeRequestPendingMail($employeeRequest, $approver, $chain));
                        $emailsSent++;

                        Log::info('✅ Email SENT Successfully', [
                            'to' => $toEmail,
                            'to_name' => $toName,
                            'from' => $fromAddress,
                            'approver_id' => $approver->id,
                        ]);
                    } catch (\Exception $mailException) {
                        $emailsFailed++;
                        Log::error('❌ Email FAILED', [
                            'to' => $toEmail,
                            'to_name' => $toName,
                            'from' => $fromAddress,
                            'error' => $mailException->getMessage(),
                            'trace' => $mailException->getTraceAsString(),
                        ]);
                    }
                } else {
                    $emailsFailed++;
                    Log::warning('⚠️ Approver has no email address', [
                        'approver_id' => $approver->id,
                        'approver_name' => $toName,
                    ]);
                }
            }

            Log::info('========== EMAIL NOTIFICATION COMPLETED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'total_approvers' => $approvers->count(),
                'emails_sent' => $emailsSent,
                'emails_failed' => $emailsFailed,
                'success_rate' => $approvers->count() > 0 ? round(($emailsSent / $approvers->count()) * 100, 2) . '%' : '0%',
            ]);

        } catch (\Exception $e) {
            Log::error('========== EMAIL NOTIFICATION FAILED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Jangan throw exception agar tidak mengganggu proses approval
        }
    }

    /**
     * Kirim notifikasi email ketika request disetujui
     */
    public function notifyApproved(EmployeeRequest $employeeRequest, User $approver, $currentLevel)
    {
        try {
            // Cek apakah email notification aktif untuk kirim_permohonan_hr
            if (!EmailNotificationSetting::isActive('kirim_permohonan_hr')) {
                Log::info('Email notification is disabled for kirim_permohonan_hr', [
                    'request_id' => $employeeRequest->id,
                    'request_number' => $employeeRequest->request_number,
                ]);
                return;
            }

            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name');
            $isFinalApproval = false;

            Log::info('========== APPROVAL EMAIL NOTIFICATION START ==========', [
                'type' => 'APPROVAL_NOTIFICATION',
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'approved_by' => $approver->name,
                'approved_by_id' => $approver->id,
                'current_level' => $currentLevel,
            ]);

            // Get approval chain
            $chain = $this->approvalService->getApprovalChain($employeeRequest);

            // Cek apakah ini approval terakhir
            $isFinalApproval = $this->isFinalApproval($employeeRequest, $chain);

            // Get next approvers for display in email
            $nextApprovers = null;
            if (!$isFinalApproval) {
                $nextApprovers = $this->getNextApprovers($employeeRequest, $chain);
            }

            // 1. Kirim email ke requester bahwa permohonan disetujui
            if ($employeeRequest->employee && $employeeRequest->employee->email) {
                $toEmail = $employeeRequest->employee->email;
                $toName = $employeeRequest->employee->name;
                $subject = $isFinalApproval
                    ? "🎉 [SIPO] Permohonan Disetujui Semua - {$employeeRequest->request_number}"
                    : "✅ [SIPO] Permohonan Disetujui - {$employeeRequest->request_number}";

                Log::info('--- Sending Approval Email to Requester ---', [
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'from_email' => $fromAddress,
                    'from_name' => $fromName,
                    'subject' => $subject,
                    'is_final_approval' => $isFinalApproval,
                    'next_approvers_count' => $nextApprovers ? $nextApprovers->count() : 0,
                ]);

                try {
                    Mail::to($toEmail)
                        ->send(new EmployeeRequestApprovedMail($employeeRequest, $approver, $currentLevel, $isFinalApproval, $nextApprovers));

                    Log::info('✅ Approval Email SENT to Requester', [
                        'to' => $toEmail,
                        'to_name' => $toName,
                        'from' => $fromAddress,
                        'is_final' => $isFinalApproval,
                    ]);
                } catch (\Exception $mailException) {
                    Log::error('❌ Approval Email FAILED to Requester', [
                        'to' => $toEmail,
                        'error' => $mailException->getMessage(),
                        'trace' => $mailException->getTraceAsString(),
                    ]);
                }
            } else {
                Log::warning('⚠️ Requester has no email', [
                    'requester_id' => $employeeRequest->employee_id ?? 'Unknown',
                ]);
            }

            // 2. Jika belum final approval, kirim notifikasi ke approver berikutnya
            if (!$isFinalApproval) {
                $nextApprovers = $this->getNextApprovers($employeeRequest, $chain);

                if ($nextApprovers->isNotEmpty()) {
                    Log::info('--- Sending Pending Emails to Next Approvers ---', [
                        'next_approvers_count' => $nextApprovers->count(),
                    ]);

                    foreach ($nextApprovers as $nextApprover) {
                        $toEmail = $nextApprover->email;
                        $toName = $nextApprover->name;
                        $subject = "🔔 [SIPO] Permohonan Baru Menunggu Approval - {$employeeRequest->request_number}";

                        Log::info('--- Sending Email to Next Approver ---', [
                            'to_email' => $toEmail,
                            'to_name' => $toName,
                            'from_email' => $fromAddress,
                            'from_name' => $fromName,
                            'subject' => $subject,
                        ]);

                        if ($toEmail) {
                            try {
                                Mail::to($toEmail)
                                    ->send(new EmployeeRequestPendingMail($employeeRequest, $nextApprover, $chain));

                                Log::info('✅ Pending Email SENT to Next Approver', [
                                    'to' => $toEmail,
                                    'to_name' => $toName,
                                    'from' => $fromAddress,
                                    'approver_id' => $nextApprover->id,
                                ]);
                            } catch (\Exception $mailException) {
                                Log::error('❌ Pending Email FAILED to Next Approver', [
                                    'to' => $toEmail,
                                    'error' => $mailException->getMessage(),
                                    'trace' => $mailException->getTraceAsString(),
                                ]);
                            }
                        }
                    }
                } else {
                    Log::info('No next approvers found (this should not happen if not final approval)');
                }
            }

            Log::info('========== APPROVAL EMAIL NOTIFICATION COMPLETED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'is_final_approval' => $isFinalApproval,
                'notified_requester' => $employeeRequest->employee && $employeeRequest->employee->email ? 'Yes' : 'No',
                'next_approvers_count' => !$isFinalApproval ? $this->getNextApprovers($employeeRequest, $chain)->count() : 0,
            ]);

        } catch (\Exception $e) {
            Log::error('========== APPROVAL EMAIL NOTIFICATION FAILED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Kirim notifikasi email ketika request ditolak
     */
    public function notifyRejected(EmployeeRequest $employeeRequest, User $approver, $currentLevel, $rejectionReason = null)
    {
        try {
            // Cek apakah email notification aktif untuk kirim_permohonan_hr
            if (!EmailNotificationSetting::isActive('kirim_permohonan_hr')) {
                Log::info('Email notification is disabled for kirim_permohonan_hr', [
                    'request_id' => $employeeRequest->id,
                    'request_number' => $employeeRequest->request_number,
                ]);
                return;
            }

            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name');

            Log::info('========== REJECTION EMAIL NOTIFICATION START ==========', [
                'type' => 'REJECTION_NOTIFICATION',
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'rejected_by' => $approver->name,
                'rejected_by_id' => $approver->id,
                'current_level' => $currentLevel,
                'rejection_reason' => $rejectionReason,
            ]);

            // Kirim email ke requester bahwa permohonan ditolak
            if ($employeeRequest->employee && $employeeRequest->employee->email) {
                $toEmail = $employeeRequest->employee->email;
                $toName = $employeeRequest->employee->name;
                $subject = "❌ [SIPO] Permohonan Ditolak - {$employeeRequest->request_number}";

                Log::info('--- Sending Rejection Email to Requester ---', [
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'from_email' => $fromAddress,
                    'from_name' => $fromName,
                    'subject' => $subject,
                    'rejection_reason' => $rejectionReason,
                ]);

                try {
                    Mail::to($toEmail)
                        ->send(new EmployeeRequestRejectedMail($employeeRequest, $approver, $currentLevel, $rejectionReason));

                    Log::info('✅ Rejection Email SENT to Requester', [
                        'to' => $toEmail,
                        'to_name' => $toName,
                        'from' => $fromAddress,
                        'rejected_by' => $approver->name,
                        'reason' => $rejectionReason,
                    ]);
                } catch (\Exception $mailException) {
                    Log::error('❌ Rejection Email FAILED to Requester', [
                        'to' => $toEmail,
                        'error' => $mailException->getMessage(),
                        'trace' => $mailException->getTraceAsString(),
                    ]);
                }
            } else {
                Log::warning('⚠️ Requester has no email', [
                    'requester_id' => $employeeRequest->employee_id ?? 'Unknown',
                ]);
            }

            Log::info('========== REJECTION EMAIL NOTIFICATION COMPLETED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'notified_requester' => $employeeRequest->employee && $employeeRequest->employee->email ? 'Yes' : 'No',
            ]);

        } catch (\Exception $e) {
            Log::error('========== REJECTION EMAIL NOTIFICATION FAILED ==========', [
                'request_id' => $employeeRequest->id,
                'request_number' => $employeeRequest->request_number,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Cek apakah approval ini adalah approval terakhir
     */
    protected function isFinalApproval(EmployeeRequest $employeeRequest, $chain)
    {
        // Cek apakah semua level sudah approve
        foreach ($chain as $level => $approverData) {
            $roleKey = $approverData['role_key'] ?? null;

            if ($roleKey === 'spv_division') {
                if (is_null($employeeRequest->supervisor_approved_at)) {
                    return false;
                }
            } elseif ($roleKey === 'head_division') {
                if (is_null($employeeRequest->head_approved_at)) {
                    return false;
                }
            } elseif ($roleKey === 'manager') {
                if (is_null($employeeRequest->manager_approved_at)) {
                    return false;
                }
            } elseif ($roleKey === 'general_manager') {
                if (is_null($employeeRequest->general_approved_at)) {
                    return false;
                }
            } elseif ($roleKey === 'hr') {
                if (is_null($employeeRequest->hr_approved_at)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get next approvers after current approval order
     */
    protected function getNextApprovers(EmployeeRequest $employeeRequest, $chain)
    {
        $currentOrder = $employeeRequest->current_approval_order ?? 0;
        $nextApprovers = collect();

        foreach ($chain as $level => $approverData) {
            $approvalOrder = $approverData['approval_order'] ?? 0;

            // Cari level dengan approval_order > currentOrder
            if ($approvalOrder > $currentOrder) {
                $users = $approverData['users'] ?? collect();
                $nextApprovers = $nextApprovers->merge($users);

                // Hanya ambil level berikutnya, bukan semua level setelahnya
                break;
            }
        }

        return $nextApprovers;
    }
}
