<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpicPaperMeeting extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meetings';

    protected $fillable = [
        'meeting_number',
        'customer_name',
        'meeting_month',
        'period_month_1',
        'period_month_2',
        'period_month_3',
        'tolerance_percentage',
        'status',
        'created_by',
        'approved_by',
        'notes',
        'submitted_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'tolerance_percentage' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relasi ke items (produk)
     */
    public function items(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingItem::class, 'meeting_id');
    }

    /**
     * Relasi ke locations
     */
    public function locations(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingLocation::class, 'meeting_id');
    }

    /**
     * Relasi ke stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingStock::class, 'meeting_id');
    }

    /**
     * Relasi ke PO remains
     */
    public function poRemains(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingPORemain::class, 'meeting_id');
    }

    /**
     * Relasi ke PO manuals
     */
    public function poManuals(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingPOManual::class, 'meeting_id');
    }

    /**
     * Relasi ke user yang membuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang approve
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate meeting number otomatis
     */
    public static function generateMeetingNumber(): string
    {
        $year = date('Y');
        $lastMeeting = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMeeting) {
            $lastNumber = (int) substr($lastMeeting->meeting_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PPIC-PM-{$year}-{$newNumber}";
    }

    /**
     * Get period string (contoh: "OKT, NOV, DES")
     */
    public function getPeriodStringAttribute(): string
    {
        return "{$this->period_month_1}, {$this->period_month_2}, {$this->period_month_3}";
    }
}

