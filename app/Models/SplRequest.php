<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SplRequest extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_spl_requests';

    protected $fillable = [
        'spl_number',
        'request_date',
        'shift',
        'start_time',
        'end_time',
        'mesin',
        'keperluan',
        'supervisor_id',
        'divisi_id',
        'status',
        'submitted_at',
        'signed_at',
        'hrd_id',
        'hrd_notes',
        'hrd_approved_at',
        'hrd_rejected_at',
        'signed_document_path',
        // Approval fields
        'head_id',
        'head_approved_at',
        'head_rejected_at',
        'head_notes',
        'manager_id',
        'manager_approved_at',
        'manager_rejected_at',
        'manager_notes',
        'general_id',
        'general_approved_at',
        'general_rejected_at',
        'general_notes',
        'current_approval_order',
    ];

    protected $casts = [
        'request_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'submitted_at' => 'datetime',
        'signed_at' => 'datetime',
        'hrd_approved_at' => 'datetime',
        'hrd_rejected_at' => 'datetime',
        'head_approved_at' => 'datetime',
        'head_rejected_at' => 'datetime',
        'manager_approved_at' => 'datetime',
        'manager_rejected_at' => 'datetime',
        'general_approved_at' => 'datetime',
        'general_rejected_at' => 'datetime',
        'current_approval_order' => 'integer',
    ];

    // Status constants (mengikuti approval flow)
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending'; // Baru submit, menunggu approval pertama
    const STATUS_SUPERVISOR_APPROVED = 'supervisor_approved';
    const STATUS_HEAD_APPROVED = 'head_approved';
    const STATUS_MANAGER_APPROVED = 'manager_approved';
    const STATUS_HR_APPROVED = 'hr_approved'; // Final approval
    const STATUS_REJECTED = 'rejected';

    // Legacy status (untuk backward compatibility)
    const STATUS_SUBMITTED = 'submitted'; // Alias untuk pending
    const STATUS_SIGNED = 'signed'; // Legacy, tidak digunakan lagi
    const STATUS_APPROVED_HRD = 'approved_hrd'; // Alias untuk hr_approved

    /**
     * Generate SPL number
     */
    public static function generateSplNumber()
    {
        $date = now()->format('Ymd');
        $lastSpl = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastSpl ? (int) substr($lastSpl->spl_number, -3) + 1 : 1;

        return 'SPL-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get supervisor
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get HRD
     */
    public function hrd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }

    /**
     * Get SPL employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(SplEmployee::class, 'spl_request_id');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Menunggu Approval',
            self::STATUS_SUBMITTED => 'Telah Dikirim', // Legacy
            self::STATUS_SIGNED => 'Sudah Ditandatangani', // Legacy
            self::STATUS_SUPERVISOR_APPROVED => 'Disetujui SPV',
            self::STATUS_HEAD_APPROVED => 'Disetujui HEAD DIVISI',
            self::STATUS_MANAGER_APPROVED => 'Disetujui MANAGER',
            self::STATUS_HR_APPROVED => 'Disetujui HRD',
            self::STATUS_APPROVED_HRD => 'Disetujui HRD', // Legacy alias
            self::STATUS_REJECTED => 'Ditolak',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get HEAD
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Get Manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get General Manager
     */
    public function general(): BelongsTo
    {
        return $this->belongsTo(User::class, 'general_id');
    }

    /**
     * Get HR (alias untuk hrd)
     */
    public function hr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }

    /**
     * Get Divisi
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Divisi::class, 'divisi_id');
    }

    /**
     * Get divisi name (accessor untuk memudahkan akses)
     */
    public function getDivisiNameAttribute()
    {
        if ($this->relationLoaded('divisi') && $this->divisi) {
            return $this->divisi->divisi;
        }

        // Fallback: query langsung jika relasi belum ter-load
        $divisi = \App\Models\Divisi::on('pgsql')->find($this->divisi_id);
        return $divisi ? $divisi->divisi : 'N/A';
    }

    /**
     * Check if all employees have signed
     */
    public function allEmployeesSigned()
    {
        return $this->employees()->where('is_signed', false)->count() === 0;
    }
}

