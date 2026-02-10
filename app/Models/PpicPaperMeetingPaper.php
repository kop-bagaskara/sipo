<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpicPaperMeetingPaper extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_papers';

    protected $fillable = [
        'meeting_item_id',
        'paper_type',
        'paper_code',
        'paper_name',
        'paper_size',
        'paper_variant',
        'up_count',
        'up_value',
        'zgsm',
        'zlength',
        'zwidth',
        'required_quantity',
        'cover_sampai',
        'minus_paper_pcs',
        'minus_paper_rim',
        'minus_paper_ton',
        'total_kebutuhan_ton',
        'catatan',
    ];

    protected $casts = [
        'required_quantity' => 'integer',
        'up_count' => 'integer',
        'up_value' => 'decimal:2',
        'zgsm' => 'decimal:2',
        'zlength' => 'decimal:2',
        'zwidth' => 'decimal:2',
        'minus_paper_pcs' => 'decimal:2',
        'minus_paper_rim' => 'decimal:6',
        'minus_paper_ton' => 'decimal:6',
        'total_kebutuhan_ton' => 'decimal:6',
    ];

    /**
     * Relasi ke meeting item
     */
    public function meetingItem(): BelongsTo
    {
        return $this->belongsTo(PpicPaperMeetingItem::class, 'meeting_item_id');
    }
}

