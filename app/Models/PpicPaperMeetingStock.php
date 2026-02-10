<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpicPaperMeetingStock extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_stocks';

    protected $fillable = [
        'meeting_id',
        'location_id',
        'paper_code',
        'paper_type',
        'stock_layer_1',
        'stock_layer_2',
        'stock_layer_3',
    ];

    protected $casts = [
        'stock_layer_1' => 'decimal:2',
        'stock_layer_2' => 'decimal:2',
        'stock_layer_3' => 'decimal:2',
    ];

    /**
     * Relasi ke meeting
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(PpicPaperMeeting::class, 'meeting_id');
    }

    /**
     * Relasi ke location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(PpicPaperMeetingLocation::class, 'location_id');
    }
}
