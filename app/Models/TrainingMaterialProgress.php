<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingMaterialProgress extends Model
{
    use HasFactory;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_material_progress';

    protected $fillable = [
        'assignment_id',
        'material_id',
        'employee_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
        'watch_duration_seconds',
        'last_position_seconds',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'watch_duration_seconds' => 'integer',
        'last_position_seconds' => 'integer',
    ];

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_WATCHING = 'watching';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TrainingAssignment::class, 'assignment_id');
    }

    /**
     * Get material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(TrainingMaterial::class, 'material_id');
    }

    /**
     * Get employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Update progress
     */
    public function updateProgress($percentage, $positionSeconds = null)
    {
        $this->progress_percentage = min(100, max(0, $percentage));

        if ($positionSeconds !== null) {
            $this->last_position_seconds = $positionSeconds;
        }

        if ($this->status === self::STATUS_NOT_STARTED) {
            $this->status = self::STATUS_WATCHING;
            $this->started_at = now();
        }

        if ($this->progress_percentage >= 100 && $this->status !== self::STATUS_COMPLETED) {
            $this->status = self::STATUS_COMPLETED;
            $this->completed_at = now();
        }

        $this->save();
    }

    /**
     * Update watch duration
     */
    public function updateWatchDuration($seconds)
    {
        $this->watch_duration_seconds += $seconds;
        $this->save();
    }
}

