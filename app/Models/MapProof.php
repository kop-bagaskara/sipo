<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapProof extends Model
{
    use HasFactory;

    protected $table = 'tb_map_proofs';

    protected $fillable = [
        'job_development_id',
        'proof_type',
        'proof_file_path',
        'customer_response',
        'customer_notes',
        'marketing_notes',
        'status',
        'sent_at',
        'created_by'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job development that owns this map proof.
     */
    public function jobDevelopment(): BelongsTo
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_development_id');
    }

    /**
     * Get the user who created this map proof.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
