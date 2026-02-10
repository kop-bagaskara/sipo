<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDevelopmentProcess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_job_development_processes';

    protected $fillable = [
        'job_development_id',
        'process_name',
        'process_type',
        'branch_type',
        'branch_conditions',
        'department_id',
        'assigned_user_id',
        'estimated_duration',
        'process_order',
        'status',
        'notes',
        'scheduled_at',
        'verification_notes',
        'verification_result',
        'started_at',
        'completed_at',
        'tracking_data',
        'verification_data'
    ];

    protected $casts = [
        'branch_conditions' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'tracking_data' => 'array',
        'verification_data' => 'array'
    ];

    // Relationships
    public function jobDevelopment()
    {
        return $this->belongsTo(JobDevelopment::class, 'job_development_id');
    }

    public function department()
    {
        return $this->belongsTo(Divisi::class, 'department_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function histories()
    {
        return $this->hasMany(JobDevelopmentProcessHistory::class, 'job_process_id');
    }
}
