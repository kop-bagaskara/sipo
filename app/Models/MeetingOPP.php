<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingOPP extends Model
{
    use HasFactory;

    protected $table = 'tb_meeting_opps';

    protected $fillable = [
        'job_development_id',
        'meeting_number',
        'meeting_date',
        'status',
        'customer_response',
        'customer_notes',
        'marketing_notes',
        'rnd_notes',
        'rnd_approval',
        'rnd_approval_notes',
        'rnd_approved_at',
        'rnd_approved_by',
        'marketing_approval',
        'marketing_approval_notes',
        'marketing_approved_at',
        'marketing_approved_by',
        'created_by',
        'returned_to_prepress',
        'returned_to_prepress_at',
        'revision_priority',
        'return_to_prepress_notes'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'rnd_approved_at' => 'datetime',
        'marketing_approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job development that owns this meeting OPP.
     */
    public function jobDevelopment(): BelongsTo
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_development_id');
    }

    /**
     * Get the user who created this meeting OPP.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
