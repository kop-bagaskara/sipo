<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpicPaperMeetingItem extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_items';

    protected $fillable = [
        'meeting_id',
        'product_code',
        'product_name',
        'product_category',
        'quantity_month_1',
        'quantity_month_2',
        'quantity_month_3',
        'total_quantity',
        'total_with_tolerance',
        'sort_order',
    ];

    protected $casts = [
        'quantity_month_1' => 'integer',
        'quantity_month_2' => 'integer',
        'quantity_month_3' => 'integer',
        'total_quantity' => 'integer',
        'total_with_tolerance' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relasi ke meeting
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(PpicPaperMeeting::class, 'meeting_id');
    }

    /**
     * Relasi ke papers (kertas)
     */
    public function papers(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingPaper::class, 'meeting_item_id');
    }
}

