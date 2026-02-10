<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchedulingDevelopment extends Model
{
    use HasFactory;

    protected $table = 'tb_scheduling_developments';

    protected $fillable = [
        'job_development_id',
        'default_days',
        'kertas_khusus_days',
        'foil_khusus_days',
        'total_estimated_days',
        'ppic_notes',
        'purchasing_notes',
        'created_by'
    ];

    protected $casts = [
        'default_days' => 'integer',
        'kertas_khusus_days' => 'integer',
        'foil_khusus_days' => 'integer',
        'total_estimated_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job development that owns this scheduling.
     */
    public function jobDevelopment(): BelongsTo
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_development_id');
    }

    /**
     * Get the user who created this scheduling.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
