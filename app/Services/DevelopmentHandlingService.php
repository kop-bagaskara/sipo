<?php

namespace App\Services;

use App\Models\HandlingDevelopment;
use App\Models\JobOrderDevelopment;
use Illuminate\Support\Facades\Auth;

class DevelopmentHandlingService
{
    /**
     * Log action untuk development job
     */
    public static function logAction(
        int $jobId,
        string $actionType,
        string $actionDescription,
        ?string $statusBefore = null,
        ?string $statusAfter = null,
        ?array $actionData = null,
        ?string $notes = null
    ): HandlingDevelopment {
        $user = Auth::user();

        return HandlingDevelopment::create([
            'job_development_id' => $jobId,
            'action_type' => $actionType,
            'action_description' => $actionDescription,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'action_data' => $actionData,
            'action_time' => now(),
            'performed_by' => $user->id,
            'performed_by_name' => $user->name,
            'notes' => $notes
        ]);
    }

    /**
     * Log job creation
     */
    public static function logJobCreated(JobOrderDevelopment $job): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'created',
            'Job development dibuat oleh marketing',
            null,
            'DRAFT',
            [
                'job_code' => $job->job_code,
                'customer' => $job->customer,
                'product' => $job->product
            ]
        );
    }

    /**
     * Log job sent to prepress
     */
    public static function logSentToPrepress(JobOrderDevelopment $job, string $prepressJobId): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'sent_to_prepress',
            'Job dikirim ke prepress oleh RnD',
            'DRAFT',
            'OPEN',
            [
                'prepress_job_id' => $prepressJobId,
                'deadline' => now()->addDays(3)->format('Y-m-d')
            ]
        );
    }

    /**
     * Log meeting OPP
     */
    public static function logMeetingOPP(JobOrderDevelopment $job, int $meetingNumber, string $status, string $customerResponse, string $customDescription = null): HandlingDevelopment
    {
        $description = $customDescription ?? "Meeting OPP {$meetingNumber} - Status: {$status}, Customer: {$customerResponse}";

        return self::logAction(
            $job->id,
            'meeting_opp_' . $meetingNumber,
            $description,
            null,
            null,
            [
                'meeting_number' => $meetingNumber,
                'status' => $status,
                'customer_response' => $customerResponse
            ]
        );
    }

    /**
     * Log scheduling development
     */
    public static function logScheduling(JobOrderDevelopment $job, int $totalDays): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'scheduling',
            "Scheduling development - Total estimated days: {$totalDays}",
            null,
            null,
            [
                'total_estimated_days' => $totalDays
            ]
        );
    }

    /**
     * Log map proof upload
     */
    public static function logMapProofUpload(JobOrderDevelopment $job, string $proofType): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'map_proof_upload',
            "Map proof diupload - Type: {$proofType}",
            null,
            null,
            [
                'proof_type' => $proofType
            ]
        );
    }

    /**
     * Log map proof sent to customer
     */
    public static function logMapProofSent(JobOrderDevelopment $job): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'map_proof_sent',
            'Map proof dikirim ke customer',
            null,
            null
        );
    }

    /**
     * Log map proof file deleted
     */
    public static function logMapProofFileDeleted(JobOrderDevelopment $job): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'map_proof_file_deleted',
            'File Map Proof dihapus',
            null,
            null
        );
    }

    /**
     * Log sales order created
     */
    public static function logSalesOrderCreated(JobOrderDevelopment $job, string $orderNumber): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'sales_order_created',
            "Sales order dibuat - Order: {$orderNumber}",
            null,
            'SALES_ORDER_CREATED',
            [
                'order_number' => $orderNumber,
            ]
        );
    }

    /**
     * Log development closed
     */
    public static function logDevelopmentClosed(JobOrderDevelopment $job): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'development_closed',
            'Development item ditutup - Alur development selesai',
            null,
            'COMPLETED',
            null,
            'Development item berhasil ditutup. Alur development telah selesai sepenuhnya.'
        );
    }

    /**
     * Log status change
     */
    public static function logStatusChange(JobOrderDevelopment $job, string $statusBefore, string $statusAfter, string $reason = null): HandlingDevelopment
    {
        return self::logAction(
            $job->id,
            'status_changed',
            "Status berubah dari {$statusBefore} ke {$statusAfter}" . ($reason ? " - {$reason}" : ''),
            $statusBefore,
            $statusAfter,
            null,
            $reason
        );
    }

    /**
     * Get timeline untuk job development
     */
    public static function getJobTimeline(int $jobId): \Illuminate\Database\Eloquent\Collection
    {
        return HandlingDevelopment::forJob($jobId)
            ->with('performedBy')
            ->orderBy('action_time', 'asc')
            ->get();
    }

    /**
     * Get report data untuk periode tertentu
     */
    public static function getReportData(string $startDate, string $endDate, ?string $actionType = null)
    {
        $query = HandlingDevelopment::dateRange($startDate, $endDate)
            ->with(['jobDevelopment', 'performedBy']);

        if ($actionType) {
            $query->actionType($actionType);
        }

        return $query->get();
    }

    /**
     * Log production schedule creation
     */
    public static function logProductionScheduled(
        int $jobId,
        string $productionDate,
        string $machineName,
        string $machineCode,
        string $status,
        ?string $notes = null
    ): HandlingDevelopment {
        $description = "Production schedule dibuat - Tanggal: {$productionDate}, Mesin: {$machineName} ({$machineCode}), Status: {$status}";

        return self::logAction(
            $jobId,
            'production_scheduled',
            $description,
            null,
            'SCHEDULED_FOR_PRODUCTION',
            null,
            $notes
        );
    }

    /**
     * Log production status update
     */
    public static function logProductionStatusUpdate(
        int $jobId,
        int $scheduleId,
        string $newStatus,
        ?string $productionNotes = null,
        ?string $qualityNotes = null
    ): HandlingDevelopment {
        $description = "Production report disubmit - Status: {$newStatus}";

        if ($productionNotes) {
            $description .= " - Production Notes: {$productionNotes}";
        }

        if ($qualityNotes) {
            $description .= " - Quality Notes: {$qualityNotes}";
        }

        return self::logAction(
            $jobId,
            'production_report_submitted',
            $description,
            null,
            $newStatus === 'completed' ? 'PRODUCTION_COMPLETED' : 'PRODUCTION_CANCELLED',
            null,
            $productionNotes
        );
    }

    /**
     * Log RnD production approval
     */
    public static function logRndProductionApproval(
        int $jobId,
        int $scheduleId,
        string $approvalStatus,
        ?string $notes = null
    ): HandlingDevelopment {
        $description = "RnD " . ($approvalStatus === 'approved' ? 'menyetujui' : 'menolak') . " production report";

        if ($notes) {
            $description .= " - Catatan: {$notes}";
        }

        return self::logAction(
            $jobId,
            'rnd_production_approval',
            $description,
            null,
            $approvalStatus === 'approved' ? 'PRODUCTION_APPROVED_BY_RND' : 'PRODUCTION_REJECTED_BY_RND',
            null,
            $notes
        );
    }

    /**
     * Log production revision
     */
    public static function logProductionRevision(
        int $jobId,
        int $scheduleId,
        ?string $productionNotes = null,
        ?string $qualityNotes = null
    ): HandlingDevelopment {
        $description = "Production report direvisi oleh tim produksi";

        if ($productionNotes) {
            $description .= " - Production Notes: {$productionNotes}";
        }

        if ($qualityNotes) {
            $description .= " - Quality Notes: {$qualityNotes}";
        }

        return self::logAction(
            $jobId,
            'production_revision',
            $description,
            null,
            'PRODUCTION_COMPLETED',
            null,
            $productionNotes
        );
    }

    /**
     * Log Production Schedule Updated
     */
    public static function logProductionScheduleUpdated($jobId, $scheduleId, $oldData, $newData)
    {
        $changes = [];

        if ($oldData['production_date'] !== $newData['production_date']) {
            $changes[] = "Tanggal: {$oldData['production_date']} → {$newData['production_date']}";
        }

        if ($oldData['production_time'] !== $newData['production_time']) {
            $changes[] = "Waktu: {$oldData['production_time']} → {$newData['production_time']}";
        }

        if ($oldData['machine_code'] !== $newData['machine_code']) {
            $changes[] = "Mesin: {$oldData['machine_name']} ({$oldData['machine_code']}) → {$newData['machine_name']} ({$newData['machine_code']})";
        }

        if ($oldData['status'] !== $newData['status']) {
            $changes[] = "Status: {$oldData['status']} → {$newData['status']}";
        }

        $changeDescription = implode(', ', $changes);

        self::logAction(
            $jobId,
            'production_schedule_updated',
            "Production schedule diupdate: {$changeDescription}",
            null,
            null,
            [
                'schedule_id' => $scheduleId,
                'changes' => $changeDescription
            ]
        );
    }

    /**
     * Log Map Proof Progress Update
     */
    public static function logMapProofProgressUpdate($job, $progress)
    {
        $progressText = '';
        switch($progress) {
            case 'proses_kirim':
                $progressText = 'PROSES KIRIM CUSTOMER';
                break;
            case 'reject':
                $progressText = 'REJECT';
                break;
            case 'accept':
                $progressText = 'ACCEPT';
                break;
        }

        self::logAction(
            $job->id,
            'map_proof_progress_update',
            "Map Proof progress diupdate: {$progressText}",
            $job->status_job,
            $job->status_job,
            [
                'progress' => $progress,
                'progress_text' => $progressText,
                'performed_by' => auth()->user()->name ?? 'Unknown'
            ]
        );
    }
}
