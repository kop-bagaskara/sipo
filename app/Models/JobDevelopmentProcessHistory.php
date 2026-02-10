<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDevelopmentProcessHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_job_development_process_histories';

    protected $fillable = [
        'job_development_id',
        'process_id',
        'user_id',
        'action_type',
        'action_result',
        'action_notes',
        'action_data',
        'action_at'
    ];

    protected $casts = [
        'action_data' => 'array',
        'action_at' => 'datetime'
    ];

    // Relationships
    public function jobDevelopment()
    {
        return $this->belongsTo(JobDevelopment::class);
    }

    public function process()
    {
        return $this->belongsTo(JobDevelopmentProcess::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
