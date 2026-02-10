<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDevelopmentQualityCheck extends Model
{
    use HasFactory;

    protected $table = 'tb_job_development_quality_checks';

    protected $fillable = [
        'job_development_id',
        'check_date',
        'result',
        'notes',
        'qc_user_id'
    ];

    protected $casts = [
        'check_date' => 'date'
    ];

    // Relationships
    public function jobDevelopment()
    {
        return $this->belongsTo(JobDevelopment::class, 'job_development_id');
    }

    public function qcUser()
    {
        return $this->belongsTo(User::class, 'qc_user_id');
    }
}
