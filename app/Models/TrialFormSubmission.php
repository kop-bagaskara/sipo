<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialFormSubmission extends Model
{
    protected $table = 'tb_trial_form_submissions';
    
    use HasFactory;

    protected $fillable = [
        'trial_process_step_id',
        'user_id',
        'form_data',
        'notes',
        'conclusion',
        'status',
        'submitted_at',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'form_data' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    // Relasi ke TrialProcessStep
    public function processStep(): BelongsTo
    {
        return $this->belongsTo(TrialProcessStep::class, 'trial_process_step_id');
    }

    // Relasi ke User yang submit
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke User yang verify
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Method untuk submit form
    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    // Method untuk verify form
    public function verify($verifiedByUserId): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedByUserId
        ]);
    }

    // Method untuk check apakah bisa di-submit
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft';
    }

    // Method untuk check apakah bisa di-verify
    public function canBeVerified(): bool
    {
        return $this->status === 'submitted';
    }

    // Method untuk get form data dengan default values
    public function getFormData($key = null, $default = null)
    {
        if ($key === null) {
            return $this->form_data ?? [];
        }

        return data_get($this->form_data, $key, $default);
    }

    // Method untuk set form data
    public function setFormData($key, $value): void
    {
        $formData = $this->form_data ?? [];
        data_set($formData, $key, $value);
        $this->form_data = $formData;
    }
}
