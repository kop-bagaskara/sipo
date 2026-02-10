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
        'material_ids',
        'status',
        'assigned_date',
        'deadline_date',
        'progress_percentage',
        'notes',
        'assigned_by',
        'updated_by',
    ];

    protected $casts = [
        'material_ids' => 'array',
        'assigned_date' => 'date',
        'deadline_date' => 'date',
        'progress_percentage' => 'decimal:2',
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
        return $this->belongsToMany(TrainingMaterial::class, 'tb_training_assignment_material', 'assignment_id', 'material_id')
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

