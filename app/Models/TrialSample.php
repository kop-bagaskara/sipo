<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialSample extends Model
{
    protected $table = 'tb_trial_samples';
    
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_pengajuan',
        'tujuan_trial',
        'material_bahan',
        'kode_barang',
        'nama_barang',
        'kode_supplier',
        'nama_supplier',
        'jumlah_bahan',
        'satuan',
        'tanggal_terima',
        'deskripsi',
        'status',
        'created_by',
        'purchasing_user_id',
        'purchasing_reviewed_at',
        'purchasing_notes',
        'qa_user_id',
        'qa_verified_at',
        'qa_notes',
        'closed_by',
        'closed_at'
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'purchasing_reviewed_at' => 'datetime',
        'qa_verified_at' => 'datetime',
        'closed_at' => 'datetime',
        'jumlah_bahan' => 'decimal:2'
    ];

    // Relasi ke User yang membuat
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User purchasing
    public function purchasingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchasing_user_id');
    }

    // Relasi ke User QA
    public function qaUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qa_user_id');
    }

    // Relasi ke User yang close
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Relasi ke process steps
    public function processSteps(): HasMany
    {
        return $this->hasMany(TrialProcessStep::class);
    }

    // Relasi ke workflow history
    public function workflowHistory(): HasMany
    {
        return $this->hasMany(TrialWorkflowHistory::class);
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // Method untuk generate nomor pengajuan
    public static function generateNomorPengajuan(): string
    {
        $prefix = 'TRIAL';
        $year = date('Y');
        $month = date('m');

        $lastTrial = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTrial ? intval(substr($lastTrial->nomor_pengajuan, -4)) + 1 : 1;

        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $sequence);
    }

    // Method untuk check apakah bisa di-approve purchasing
    public function canBeApprovedByPurchasing(): bool
    {
        return $this->status === 'purchasing_review';
    }

    // Method untuk check apakah bisa di-approve QA
    public function canBeApprovedByQA(): bool
    {
        return $this->status === 'qa_completed';
    }

    // Method untuk check apakah bisa di-close
    public function canBeClosed(): bool
    {
        return $this->status === 'qa_verified';
    }

    // Method untuk check apakah semua process steps sudah selesai
    public function allProcessStepsCompleted(): bool
    {
        return $this->processSteps()
            ->where('status', '!=', 'completed')
            ->count() === 0;
    }

    // Method untuk update status berdasarkan process steps
    public function updateStatusBasedOnProcessSteps(): void
    {
        if ($this->status === 'qa_processing') {
            if ($this->allProcessStepsCompleted()) {
                $this->update(['status' => 'qa_completed']);
            }
        }
    }
}
