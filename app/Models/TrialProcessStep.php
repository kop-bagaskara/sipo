<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrialProcessStep extends Model
{
    protected $table = 'tb_trial_process_steps';
    
    use HasFactory;

    protected $fillable = [
        'trial_sample_id',
        'urutan',
        'proses',
        'department_terkait',
        'rencana_trial',
        'mesin',
        'status',
        'assigned_user_id',
        'assigned_at',
        'started_at',
        'completed_at',
        'verified_at',
        'notes'
    ];

    protected $casts = [
        'rencana_trial' => 'date',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    // Relasi ke TrialSample
    public function trialSample(): BelongsTo
    {
        return $this->belongsTo(TrialSample::class);
    }

    // Relasi ke User yang ditugaskan
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // Relasi ke form submissions
    public function formSubmissions(): HasMany
    {
        return $this->hasMany(TrialFormSubmission::class);
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk user tertentu
    public function scopeByAssignedUser($query, $userId)
    {
        return $query->where('assigned_user_id', $userId);
    }

    // Method untuk assign user
    public function assignUser($userId): void
    {
        $this->update([
            'assigned_user_id' => $userId,
            'assigned_at' => now(),
            'status' => 'assigned'
        ]);
    }

    // Method untuk start process
    public function startProcess(): void
    {
        $this->update([
            'started_at' => now(),
            'status' => 'in_progress'
        ]);
    }

    // Method untuk complete process
    public function completeProcess(): void
    {
        $this->update([
            'completed_at' => now(),
            'status' => 'completed'
        ]);

        // Update trial sample status
        $this->trialSample->updateStatusBasedOnProcessSteps();
    }

    // Method untuk verify process
    public function verifyProcess(): void
    {
        $this->update([
            'verified_at' => now(),
            'status' => 'verified'
        ]);
    }

    // Method untuk check apakah bisa di-complete
    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    // Method untuk check apakah bisa di-verify
    public function canBeVerified(): bool
    {
        return $this->status === 'completed';
    }
}
