<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDevelopment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_job_developments';

    protected $fillable = [
        'job_code',
        'job_name',
        'specification',
        'attachment',
        'type',
        'priority',
        'expected_completion',
        'customer_name',
        'status',
        'marketing_user_id',
        'rnd_user_id',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expected_completion' => 'date'
    ];

    // Relationships
    public function marketingUser()
    {
        return $this->belongsTo(User::class, 'marketing_user_id');
    }

    public function rndUser()
    {
        return $this->belongsTo(User::class, 'rnd_user_id');
    }

    public function processes()
    {
        return $this->hasMany(JobDevelopmentProcess::class, 'job_development_id');
    }

    public function purchasingItems()
    {
        return $this->hasMany(JobDevelopmentPurchasingItem::class, 'job_development_id');
    }

    public function qualityChecks()
    {
        return $this->hasMany(JobDevelopmentQualityCheck::class, 'job_development_id');
    }

    public function approval()
    {
        return $this->hasOne(JobDevelopmentApproval::class, 'job_development_id');
    }
}
