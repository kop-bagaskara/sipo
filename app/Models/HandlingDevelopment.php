<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandlingDevelopment extends Model
{
    use HasFactory;

    protected $table = 'tb_handling_developments';

    protected $fillable = [
        'job_development_id',
        'action_type',
        'action_description',
        'status_before',
        'status_after',
        'action_data',
        'action_time',
        'performed_by',
        'performed_by_name',
        'notes'
    ];

    protected $casts = [
        'action_data' => 'array',
        'action_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job development that owns this handling record.
     */
    public function jobDevelopment(): BelongsTo
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_development_id');
    }

    /**
     * Get the user who performed this action.
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope untuk filter berdasarkan action type
     */
    public function scopeActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope untuk filter berdasarkan job development
     */
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_development_id', $jobId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('action_time', [$startDate, $endDate]);
    }
}
