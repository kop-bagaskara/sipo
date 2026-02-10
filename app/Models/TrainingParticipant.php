<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingParticipant extends Model
{
    use HasFactory;

    // Use the HR PostgreSQL connection
    protected $connection = 'pgsql2';

    protected $table = 'tb_training_participants';

    protected $fillable = [
        'training_id',
        'employee_id',
        'registration_status',
        'registration_type',
        'attendance_status',
        'registered_at',
        'approved_at',
        'rejected_at',
        'attended_at',
        'completed_at',
        'cancelled_at',
        'rejection_reason',
        'cancellation_reason',
        'score',
        'feedback',
        'instructor_notes',
        'attendance_data',
        'assessment_data',
        'certificate_issued',
        'certificate_number',
        'certificate_issued_at',
        'notes',
        'approved_by',
        'rejected_by'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attended_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'attendance_data' => 'array',
        'assessment_data' => 'array',
        'certificate_issued' => 'boolean',
        'score' => 'decimal:2'
    ];

    // Registration status constants
    const STATUS_REGISTERED = 'registered';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ATTENDED = 'attended';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Registration type constants
    const TYPE_MANDATORY = 'mandatory';
    const TYPE_VOLUNTARY = 'voluntary';
    const TYPE_RECOMMENDED = 'recommended';

    /**
     * Get the training
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingMaster::class, 'training_id');
    }

    /**
     * Get the employee from paytest database
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(PaytestEmployee::class, 'employee_id', 'Nip');
    }

    /**
     * Get the user who approved this registration
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected this registration
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if participant is registered
     */
    public function isRegistered()
    {
        return $this->registration_status === self::STATUS_REGISTERED;
    }

    /**
     * Check if participant is approved
     */
    public function isApproved()
    {
        return $this->registration_status === self::STATUS_APPROVED;
    }

    /**
     * Check if participant is rejected
     */
    public function isRejected()
    {
        return $this->registration_status === self::STATUS_REJECTED;
    }

    /**
     * Check if participant attended
     */
    public function isAttended()
    {
        return $this->registration_status === self::STATUS_ATTENDED;
    }

    /**
     * Check if participant completed
     */
    public function isCompleted()
    {
        return $this->registration_status === self::STATUS_COMPLETED;
    }

    /**
     * Check if participant is cancelled
     */
    public function isCancelled()
    {
        return $this->registration_status === self::STATUS_CANCELLED;
    }

    /**
     * Check if registration is mandatory
     */
    public function isMandatory()
    {
        return $this->registration_type === self::TYPE_MANDATORY;
    }

    /**
     * Check if registration is voluntary
     */
    public function isVoluntary()
    {
        return $this->registration_type === self::TYPE_VOLUNTARY;
    }

    /**
     * Check if registration is recommended
     */
    public function isRecommended()
    {
        return $this->registration_type === self::TYPE_RECOMMENDED;
    }

    /**
     * Check if certificate is issued
     */
    public function hasCertificate()
    {
        return $this->certificate_issued && !empty($this->certificate_number);
    }

    /**
     * Check if participant is present
     */
    public function isPresent()
    {
        return $this->attendance_status === 'present';
    }

    /**
     * Check if participant is absent
     */
    public function isAbsent()
    {
        return $this->attendance_status === 'absent';
    }

    /**
     * Check if participant is late
     */
    public function isLate()
    {
        return $this->attendance_status === 'late';
    }

    /**
     * Get registration duration in days
     */
    public function getRegistrationDurationAttribute()
    {
        if (!$this->registered_at) {
            return null;
        }

        $endDate = $this->approved_at ?? $this->rejected_at ?? $this->cancelled_at ?? now();
        return $this->registered_at->diffInDays($endDate);
    }

    /**
     * Get training duration in days
     */
    public function getTrainingDurationAttribute()
    {
        if (!$this->attended_at) {
            return null;
        }

        $endDate = $this->completed_at ?? now();
        return $this->attended_at->diffInDays($endDate);
    }

    /**
     * Scope for registered participants
     */
    public function scopeRegistered($query)
    {
        return $query->where('registration_status', self::STATUS_REGISTERED);
    }

    /**
     * Scope for approved participants
     */
    public function scopeApproved($query)
    {
        return $query->where('registration_status', self::STATUS_APPROVED);
    }

    /**
     * Scope for completed participants
     */
    public function scopeCompleted($query)
    {
        return $query->where('registration_status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for mandatory registrations
     */
    public function scopeMandatory($query)
    {
        return $query->where('registration_type', self::TYPE_MANDATORY);
    }

    /**
     * Scope for voluntary registrations
     */
    public function scopeVoluntary($query)
    {
        return $query->where('registration_type', self::TYPE_VOLUNTARY);
    }

    /**
     * Scope for participants with certificates
     */
    public function scopeWithCertificates($query)
    {
        return $query->where('certificate_issued', true);
    }
}
