<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_assignments';

    protected $fillable = [
        'training_id',
        'employee_id',
        'session_code',
        'material_ids',
        'status',
        'is_opened',
        'opened_at',
        'assigned_date',
        'start_date',
        'deadline_date',
        'progress_percentage',
        'notes',
        'assigned_by',
        'updated_by',
    ];

    protected $casts = [
        'material_ids' => 'array',
        'assigned_date' => 'date',
        'start_date' => 'date',
        'deadline_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'is_opened' => 'boolean',
        'opened_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get assigned by user
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get training
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingMaster::class, 'training_id');
    }

    /**
     * Get material progress
     */
    public function materialProgress(): HasMany
    {
        return $this->hasMany(TrainingMaterialProgress::class, 'assignment_id');
    }

    /**
     * Get progress (alias for materialProgress)
     */
    public function progress(): HasMany
    {
        return $this->materialProgress();
    }

    /**
     * Get exams
     */
    public function exams(): HasMany
    {
        return $this->hasMany(TrainingExam::class, 'assignment_id');
    }

    /**
     * Get result
     */
    public function result(): HasMany
    {
        return $this->hasMany(TrainingResult::class, 'assignment_id');
    }

    /**
     * Get session progress
     */
    public function sessionProgress(): HasMany
    {
        return $this->hasMany(TrainingSessionProgress::class, 'assignment_id');
    }

    /**
     * Get materials (from material_ids JSON)
     */
    public function getMaterialsAttribute()
    {
        if (!$this->material_ids) {
            return collect();
        }

        $materialIds = is_array($this->material_ids)
            ? $this->material_ids
            : json_decode($this->material_ids, true);

        return TrainingMaterial::whereIn('id', $materialIds)->get();
    }

    /**
     * Materials relationship (for pivot table operations)
     */
    public function materials()
    {
        return $this->belongsToMany(
            TrainingMaterial::class, 
            'tb_training_assignment_material', 
            'assignment_id', 
            'material_id',
            'id',
            'id'
        )
            ->using(\Illuminate\Database\Eloquent\Relations\Pivot::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    /**
     * Check if assignment is expired
     */
    public function isExpired()
    {
        if (!$this->deadline_date) {
            return false;
        }

        return now()->greaterThan($this->deadline_date) && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Calculate progress percentage
     */
    public function calculateProgress()
    {
        $materials = $this->materials;
        if ($materials->isEmpty()) {
            return 0;
        }

        $completedCount = $this->materialProgress()
            ->where('status', TrainingMaterialProgress::STATUS_COMPLETED)
            ->count();

        return ($completedCount / $materials->count()) * 100;
    }

    /**
     * Calculate session progress percentage
     */
    public function calculateSessionProgress()
    {
        $sessions = $this->training->sessions ?? collect();
        if ($sessions->isEmpty()) {
            return 0;
        }

        $completedCount = $this->sessionProgress()
            ->whereIn('status', [TrainingSessionProgress::STATUS_PASSED, TrainingSessionProgress::STATUS_COMPLETED])
            ->count();

        return ($completedCount / $sessions->count()) * 100;
    }

    /**
     * Get current session (the next session to be completed)
     */
    public function getCurrentSession()
    {
        $allSessions = $this->training->sessions()->active()->ordered()->get();

        foreach ($allSessions as $session) {
            $progress = $this->sessionProgress()
                ->where('session_id', $session->id)
                ->first();

            // If no progress or not passed, this is the current session
            if (!$progress || !in_array($progress->status, [TrainingSessionProgress::STATUS_PASSED, TrainingSessionProgress::STATUS_COMPLETED])) {
                return $session;
            }
        }

        return null; // All sessions completed
    }

    /**
     * Get current session progress
     */
    public function getCurrentSessionProgress()
    {
        $currentSession = $this->getCurrentSession();

        if (!$currentSession) {
            return null;
        }

        return $this->sessionProgress()
            ->where('session_id', $currentSession->id)
            ->first();
    }

    /**
     * Check if all sessions are completed
     * A session is considered completed if it has been submitted (not not_started or in_progress)
     */
    public function isAllSessionsCompleted()
    {
        $totalSessions = $this->training->sessions()->active()->count();

        if ($totalSessions === 0) {
            return true;
        }

        // Get all sessions for this training
        $allSessions = $this->training->sessions()->active()->get();
        
        // Check if all sessions have been submitted (have progress with status that is not not_started or in_progress)
        foreach ($allSessions as $session) {
            $progress = $this->sessionProgress()
                ->where('session_id', $session->id)
                ->first();
            
            // If no progress or still in progress/not started, not all sessions are completed
            if (!$progress || in_array($progress->status, [
                TrainingSessionProgress::STATUS_NOT_STARTED,
                TrainingSessionProgress::STATUS_IN_PROGRESS
            ])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope by employee
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

