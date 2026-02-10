<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MasterProsesDevelopment extends Model
{
    use HasFactory;

    protected $table = 'tb_master_proses_developments';

    protected $fillable = [
        'job_order_development_id',
        'proses_name',
        'urutan_proses',
        'department_responsible',
        'status_proses',
        'notes',
        'started_at',
        'completed_at',
        'completed_by',
        'expected_days',
        'is_required'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_required' => 'boolean',
    ];

    // Relationships
    public function jobOrderDevelopment()
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_order_development_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-secondary',
            'in_progress' => 'badge-warning',
            'completed' => 'badge-success',
            'skipped' => 'badge-info'
        ];

        return $badges[$this->status_proses] ?? 'badge-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'skipped' => 'Skipped'
        ];

        return $texts[$this->status_proses] ?? 'Unknown';
    }

    public function getDepartmentBadgeAttribute()
    {
        $badges = [
            'Marketing' => 'badge-primary',
            'RnD' => 'badge-info',
            'Prepress' => 'badge-secondary',
            'Customer' => 'badge-warning',
            'PPIC' => 'badge-warning',
            'Production' => 'badge-success'
        ];

        return $badges[$this->department_responsible] ?? 'badge-secondary';
    }

    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInDays($this->completed_at);
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status_proses !== 'completed' && $this->started_at) {
            // Check if process is overdue (more than expected days)
            $expectedDays = $this->getExpectedDays();
            if ($expectedDays && $this->started_at->addDays($expectedDays)->isPast()) {
                return true;
            }
        }

        return false;
    }

    private function getExpectedDays()
    {
        // Expected days for each process
        $expectedDays = [
            1 => 1,   // Marketing input job development
            2 => 1,   // RnD send to prepress
            3 => 3,   // Prepress process
            4 => 1,   // Marketing create Meeting OPP
            5 => 2,   // Customer ACC/REJECT Meeting OPP
            6 => 2,   // PPIC scheduling development & production
            7 => 5,   // Production report results
            8 => 1,   // RnD approve production report
            9 => 1,   // Marketing upload Map Proof
            10 => 2,  // Customer ACC Map Proof
            11 => 1,  // Marketing create Sales Order
            12 => 1   // Marketing close development item
        ];

        return $expectedDays[$this->urutan_proses] ?? null;
    }

    // Scopes
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department_responsible', $department);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_proses', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status_proses', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status_proses', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_proses', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status_proses', 'in_progress')
                    ->where('started_at', '<', Carbon::now()->subDays(3)); // Overdue if more than 3 days
    }

    // Methods
    public function startProcess($userId = null)
    {
        $this->update([
            'status_proses' => 'in_progress',
            'started_at' => now(),
            'completed_by' => $userId
        ]);
    }

    public function completeProcess($userId = null, $notes = null)
    {
        $this->update([
            'status_proses' => 'completed',
            'completed_at' => now(),
            'completed_by' => $userId,
            'notes' => $notes
        ]);
    }

    public function skipProcess($userId = null, $notes = null)
    {
        $this->update([
            'status_proses' => 'skipped',
            'completed_at' => now(),
            'completed_by' => $userId,
            'notes' => $notes
        ]);
    }

    public function canStart()
    {
        // Check if previous process is completed
        $previousProcess = self::where('job_order_development_id', $this->job_order_development_id)
                              ->where('urutan_proses', $this->urutan_proses - 1)
                              ->first();

        if (!$previousProcess) {
            return true; // First process
        }

        return $previousProcess->status_proses === 'completed';
    }

    public function isBlockingNext()
    {
        return $this->status_proses === 'pending' || $this->status_proses === 'in_progress';
    }
}
