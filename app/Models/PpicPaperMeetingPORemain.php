<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpicPaperMeetingPORemain extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_po_remains';

    protected $fillable = [
        'meeting_id',
        'po_doc_no',
        'paper_code',
        'paper_type',
        'qty_remain',
        'po_remain_layer_1',
        'po_remain_layer_2',
        'up_value',
    ];

    protected $casts = [
        'qty_remain' => 'decimal:2',
        'po_remain_layer_1' => 'decimal:2',
        'po_remain_layer_2' => 'decimal:2',
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
