<?php

namespace App\Services;

use App\Models\MasterProsesDevelopment;
use App\Models\JobOrderDevelopment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MasterProsesService
{
    // Master proses configuration
    private $masterProses = [
        1 => [
            'name' => 'Marketing Input Job Development',
            'department' => 'Marketing',
            'description' => 'Marketing membuat job development baru dan langsung dikirim ke prepress'
        ],
        2 => [
            'name' => 'Prepress Process',
            'department' => 'Prepress',
            'description' => 'Prepress memproses job yang dikirim langsung dari marketing'
        ],
        3 => [
            'name' => 'Marketing Create Meeting OPP',
            'department' => 'Marketing',
            'description' => 'Marketing membuat Meeting OPP untuk customer'
        ],
        4 => [
            'name' => 'Customer ACC/REJECT Meeting OPP',
            'department' => 'Customer',
            'description' => 'Customer memberikan approval atau reject Meeting OPP'
        ],
        5 => [
            'name' => 'PPIC Scheduling Development & Production',
            'department' => 'PPIC',
            'description' => 'PPIC melakukan scheduling development dan production'
        ],
        6 => [
            'name' => 'Production Report Results',
            'department' => 'Production',
            'description' => 'Production melaporkan hasil produksi'
        ],
        7 => [
            'name' => 'RnD Approve Production Report',
            'department' => 'RnD',
            'description' => 'RnD memberikan approval untuk production report'
        ],
        8 => [
            'name' => 'Marketing Upload Map Proof',
            'department' => 'Marketing',
            'description' => 'Marketing upload Map Proof ke customer'
        ],
        9 => [
            'name' => 'Customer ACC Map Proof',
            'department' => 'Customer',
            'description' => 'Customer memberikan approval untuk Map Proof'
        ],
        10 => [
            'name' => 'Marketing Create Sales Order',
            'department' => 'Marketing',
            'description' => 'Marketing membuat Sales Order'
        ],
        11 => [
            'name' => 'Marketing Close Development Item',
            'department' => 'Marketing',
            'description' => 'Marketing menutup development item'
        ]
    ];

    /**
     * Initialize master proses for a job development
     */
    public function initializeMasterProses($jobId)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($jobId);

            // Check if master proses already exists
            $existingProses = MasterProsesDevelopment::where('job_order_development_id', $jobId)->count();
            if ($existingProses > 0) {
                Log::info("Master proses already exists for job {$jobId}");
                return false;
            }

            // Create all master proses
            foreach ($this->masterProses as $urutan => $proses) {
                MasterProsesDevelopment::create([
                    'job_order_development_id' => $jobId,
                    'proses_name' => $proses['name'],
                    'urutan_proses' => $urutan,
                    'department_responsible' => $proses['department'],
                    'status_proses' => 'pending'
                ]);
            }

            Log::info("Master proses initialized for job {$jobId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error initializing master proses for job {$jobId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update proses status
     */
    public function updateProsesStatus($jobId, $urutanProses, $status, $userId = null, $notes = null)
    {
        try {
            $proses = MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                           ->where('urutan_proses', $urutanProses)
                                           ->first();

            if (!$proses) {
                Log::error("Proses not found for job {$jobId}, urutan {$urutanProses}");
                return false;
            }

            switch ($status) {
                case 'start':
                    $proses->startProcess($userId);
                    break;
                case 'complete':
                    $proses->completeProcess($userId, $notes);
                    break;
                case 'skip':
                    $proses->skipProcess($userId, $notes);
                    break;
                default:
                    $proses->update(['status_proses' => $status]);
            }

            Log::info("Proses {$urutanProses} updated to {$status} for job {$jobId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error updating proses status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get master proses for a job
     */
    public function getMasterProses($jobId)
    {
        return MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                     ->orderBy('urutan_proses')
                                     ->get();
    }

    /**
     * Get current proses (next pending)
     */
    public function getCurrentProses($jobId)
    {
        return MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                     ->where('status_proses', 'pending')
                                     ->orderBy('urutan_proses')
                                     ->first();
    }

    /**
     * Get completed proses count
     */
    public function getCompletedCount($jobId)
    {
        return MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                     ->where('status_proses', 'completed')
                                     ->count();
    }

    /**
     * Get total proses count
     */
    public function getTotalCount($jobId)
    {
        return MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                     ->count();
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage($jobId)
    {
        $completed = $this->getCompletedCount($jobId);
        $total = $this->getTotalCount($jobId);

        if ($total == 0) return 0;

        return round(($completed / $total) * 100);
    }

    /**
     * Get department workload
     */
    public function getDepartmentWorkload($department = null)
    {
        $query = MasterProsesDevelopment::with(['jobOrderDevelopment', 'completedBy']);

        if ($department) {
            $query->where('department_responsible', $department);
        }

        return $query->get()->groupBy('status_proses');
    }

    /**
     * Get overdue proses
     */
    public function getOverdueProses()
    {
        return MasterProsesDevelopment::with(['jobOrderDevelopment', 'completedBy'])
                                     ->overdue()
                                     ->get();
    }

    /**
     * Get proses statistics
     */
    public function getProsesStatistics()
    {
        $stats = [
            'total' => MasterProsesDevelopment::count(),
            'pending' => MasterProsesDevelopment::pending()->count(),
            'in_progress' => MasterProsesDevelopment::inProgress()->count(),
            'completed' => MasterProsesDevelopment::completed()->count(),
            'overdue' => MasterProsesDevelopment::overdue()->count()
        ];

        // Department breakdown
        $stats['by_department'] = MasterProsesDevelopment::selectRaw('department_responsible, status_proses, COUNT(*) as count')
                                                         ->groupBy('department_responsible', 'status_proses')
                                                         ->get()
                                                         ->groupBy('department_responsible');

        return $stats;
    }

    /**
     * Check if job can proceed to next step
     */
    public function canProceedToNext($jobId, $urutanProses)
    {
        $proses = MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                       ->where('urutan_proses', $urutanProses)
                                       ->first();

        if (!$proses) return false;

        return $proses->canStart();
    }

    /**
     * Get blocking proses
     */
    public function getBlockingProses($jobId)
    {
        return MasterProsesDevelopment::where('job_order_development_id', $jobId)
                                     ->whereIn('status_proses', ['pending', 'in_progress'])
                                     ->orderBy('urutan_proses')
                                     ->get();
    }

    /**
     * Get master proses configuration
     */
    public function getMasterProsesConfig()
    {
        return $this->masterProses;
    }
}
