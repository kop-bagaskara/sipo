<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDevelopmentApproval extends Model
{
    use HasFactory;

    protected $table = 'tb_job_development_approvals';

    protected $fillable = [
        'job_development_id',
        'approval_status',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function jobDevelopment()
    {
        return $this->belongsTo(JobDevelopment::class, 'job_development_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
