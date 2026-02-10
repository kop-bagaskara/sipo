<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpicPaperMeetingPOManual extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_po_manuals';

    protected $fillable = [
        'meeting_id',
        'paper_code',
        'paper_type',
        'po_manual_layer_1',
        'po_manual_layer_2',
        'up_value',
    ];

    protected $casts = [
        'po_manual_layer_1' => 'decimal:2',
        'po_manual_layer_2' => 'decimal:2',
        'up_value' => 'decimal:2',
    ];

    /**
     * Relasi ke meeting
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(PpicPaperMeeting::class, 'meeting_id');
    }
}
