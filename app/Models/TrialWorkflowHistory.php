<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialWorkflowHistory extends Model
{
    use HasFactory;

    protected $table = 'trial_workflow_history';

    protected $fillable = [
        'trial_sample_id',
        'user_id',
        'action',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Relasi ke TrialSample
    public function trialSample(): BelongsTo
    {
        return $this->belongsTo(TrialSample::class);
    }

    // Relasi ke User yang melakukan action
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk action tertentu
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Method untuk log action
    public static function logAction($trialSampleId, $userId, $action, $notes = null, $metadata = null): self
    {
        return self::create([
            'trial_sample_id' => $trialSampleId,
            'user_id' => $userId,
            'action' => $action,
            'notes' => $notes,
            'metadata' => $metadata
        ]);
    }

    // Method untuk get readable action description
    public function getActionDescription(): string
    {
        $descriptions = [
            'created' => 'Pengajuan dibuat',
            'submitted' => 'Pengajuan disubmit ke purchasing',
            'purchasing_review' => 'Purchasing mulai review',
            'purchasing_approved' => 'Purchasing approve',
            'purchasing_rejected' => 'Purchasing reject',
            'qa_processing' => 'QA mulai proses',
            'step_assigned' => 'Step di-assign ke user',
            'step_started' => 'User mulai kerjakan step',
            'step_completed' => 'User selesai step',
            'step_verified' => 'QA verifikasi step',
            'qa_completed' => 'Semua step selesai',
            'qa_verified' => 'QA verifikasi final',
            'closed' => 'Pengajuan di-close'
        ];

        return $descriptions[$this->action] ?? $this->action;
    }
}
