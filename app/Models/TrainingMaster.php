<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingMaster extends Model
{
    use HasFactory, SoftDeletes;

    // Use pgsql3 for Portal Training
    protected $connection = 'pgsql3';

    protected $table = 'tb_training_masters';

    protected $fillable = [
        'training_code',
        'training_name',
        'description',
        'objectives',
        'prerequisites',
        'training_type',
        'training_method',
        'duration_hours',
        'max_participants',
        'min_participants',
        'cost_per_participant',
        'instructor_name',
        'instructor_contact',
        'status',
        'is_active',
        'allow_retry',
        'target_departments',
        'target_positions',
        'target_levels',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

    protected $casts = [
        'target_departments' => 'array',
        'target_positions' => 'array',
        'target_levels' => 'array',
        'cost_per_participant' => 'decimal:2',
        'is_active' => 'boolean',
        'allow_retry' => 'boolean'
    ];

    // Training types
    const TYPE_MANDATORY = 'mandatory';
    const TYPE_OPTIONAL = 'optional';
    const TYPE_CERTIFICATION = 'certification';
    const TYPE_SKILL_DEVELOPMENT = 'skill_development';

    // Training methods
    const METHOD_CLASSROOM = 'classroom';
    const METHOD_ONLINE = 'online';
    const METHOD_HYBRID = 'hybrid';
    const METHOD_WORKSHOP = 'workshop';
    const METHOD_SEMINAR = 'seminar';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot method to generate training code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->training_code)) {
                $model->training_code = $model->generateTrainingCode();
            }
        });
    }

    /**
     * Generate unique training code
     */
    public function generateTrainingCode()
    {
        $prefix = 'TRG-';
        $year = date('y');
        $month = date('m');
        $day = date('d');

        $lastTraining = static::whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', $month)
                            ->whereDay('created_at', $day)
                            ->orderBy('id', 'desc')
                            ->first();

        $sequence = $lastTraining ? (intval(substr($lastTraining->training_code, -4)) + 1) : 1;

        return $prefix . $year . $month . $day . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who created this training
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this training
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get training participants
     */
    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class, 'training_id');
    }

    /**
     * Get training schedules
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class, 'training_id');
    }

    /**
     * Get training sessions
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'training_id');
    }

    /**
     * Get departments associated with this training (from JSON field)
     */
    public function getDepartmentsAttribute()
    {
        if (!$this->target_departments) {
            return collect();
        }

        $departmentIds = is_array($this->target_departments)
            ? $this->target_departments
            : json_decode($this->target_departments, true);

        return Divisi::whereIn('id', $departmentIds)->get();
    }

    /**
     * Get approved participants
     */
    public function approvedParticipants()
    {
        return $this->participants()->where('registration_status', 'approved');
    }

    /**
     * Get completed participants
     */
    public function completedParticipants()
    {
        return $this->participants()->where('registration_status', 'completed');
    }

    /**
     * Get participants count
     */
    public function getParticipantsCountAttribute()
    {
        return $this->participants()->count();
    }

    /**
     * Get approved participants count
     */
    public function getApprovedParticipantsCountAttribute()
    {
        return $this->approvedParticipants()->count();
    }

    /**
     * Get completed participants count
     */
    public function getCompletedParticipantsCountAttribute()
    {
        return $this->completedParticipants()->count();
    }

    /**
     * Check if training is full
     */
    public function isFull()
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->approvedParticipants()->count() >= $this->max_participants;
    }

    /**
     * Check if employee can register for this training
     */
    public function canEmployeeRegister($employeeId)
    {
        // Check if employee already registered
        $existingParticipant = $this->participants()
            ->where('employee_id', $employeeId)
            ->whereIn('registration_status', ['registered', 'approved', 'attended', 'completed'])
            ->first();

        if ($existingParticipant) {
            return false;
        }

        // Check if training is full
        if ($this->isFull()) {
            return false;
        }

        // Check if training is active and published
        if (!$this->is_active || $this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active trainings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for published trainings
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for mandatory trainings
     */
    public function scopeMandatory($query)
    {
        return $query->where('training_type', self::TYPE_MANDATORY);
    }

    /**
     * Scope for trainings by department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->whereJsonContains('target_departments', $departmentId);
    }

    /**
     * Get training materials
     * TrainingMaster dan TrainingMaterial sama-sama di pgsql3
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            TrainingMaterial::class,
            'tb_training_master_material', // Pivot table di pgsql3
            'training_id',
            'material_id',
            'id',
            'id'
        )
            ->using(\Illuminate\Database\Eloquent\Relations\Pivot::class)
            ->withPivot('display_order')
            ->withTimestamps()
            ->select('tb_training_materials.*', 'tb_training_master_material.display_order')
            ->orderBy('tb_training_master_material.display_order', 'asc');
    }
}
