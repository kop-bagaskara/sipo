<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingDepartment extends Model
{
    use HasFactory;

    // Use the HR PostgreSQL connection
    protected $connection = 'pgsql2';

    protected $table = 'tb_training_departments';

    protected $fillable = [
        'training_id',
        'department_id',
        'is_mandatory',
        'priority',
        'notes'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean'
    ];

    /**
     * Get the training
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingMaster::class, 'training_id');
    }

    /**
     * Get the department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'department_id');
    }

    /**
     * Check if training is mandatory for this department
     */
    public function isMandatory()
    {
        return $this->is_mandatory;
    }

    /**
     * Get priority level
     */
    public function getPriorityLevelAttribute()
    {
        switch ($this->priority) {
            case 1:
                return 'High';
            case 2:
                return 'Medium';
            case 3:
                return 'Low';
            default:
                return 'Unknown';
        }
    }

    /**
     * Scope for mandatory trainings
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope for high priority trainings
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 1);
    }

    /**
     * Scope for medium priority trainings
     */
    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 2);
    }

    /**
     * Scope for low priority trainings
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', 3);
    }
}
