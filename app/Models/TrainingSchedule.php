<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';

    protected $table = 'tb_training_schedules';

    protected $fillable = [
        'training_id',
        'schedule_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'status',
        'created_by',
        // Validation fields
        'attendance_validation_status',
        'attendance_validated_at',
        'validated_by',
        'validation_notes',
        // Participant tracking
        'total_participants',
        'attended_participants',
        'absent_participants',
        'reschedule_needed',
        // Training status
        'training_status',
        'training_completed_at',
        'training_cancelled_at',
        'cancellation_reason',
        // Certificate fields
        'certificates_issued',
        'certificates_count',
        'certificates_issued_at'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'attendance_validated_at' => 'datetime',
        'training_completed_at' => 'datetime',
        'training_cancelled_at' => 'datetime',
        'certificates_issued_at' => 'datetime',
        'certificates_issued' => 'boolean',
        'total_participants' => 'integer',
        'attended_participants' => 'integer',
        'absent_participants' => 'integer',
        'reschedule_needed' => 'integer',
        'certificates_count' => 'integer'
    ];

    /**
     * Get the training that owns the schedule
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingMaster::class, 'training_id');
    }

    /**
     * Get the user who created the schedule
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who validated the attendance
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get participants for this training schedule
     */
    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class, 'training_id', 'training_id');
    }

    /**
     * Check if schedule is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->schedule_date >= now()->toDateString() && $this->status === 'scheduled';
    }

    /**
     * Check if schedule is today
     */
    public function isToday(): bool
    {
        return $this->schedule_date->isToday();
    }

    /**
     * Get formatted schedule time
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    /**
     * Get formatted schedule date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->schedule_date->format('d/m/Y');
    }

    /**
     * Check if attendance is validated
     */
    public function isAttendanceValidated(): bool
    {
        return $this->attendance_validation_status === 'validated';
    }

    /**
     * Check if training is completed
     */
    public function isTrainingCompleted(): bool
    {
        return $this->training_status === 'completed';
    }

    /**
     * Check if training is cancelled
     */
    public function isTrainingCancelled(): bool
    {
        return $this->training_status === 'cancelled';
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute(): float
    {
        if ($this->total_participants === 0) {
            return 0;
        }

        return round(($this->attended_participants / $this->total_participants) * 100, 2);
    }

    /**
     * Check if certificates are issued
     */
    public function hasCertificatesIssued(): bool
    {
        return $this->certificates_issued && $this->certificates_count > 0;
    }
}
