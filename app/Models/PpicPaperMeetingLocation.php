<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpicPaperMeetingLocation extends Model
{
    use HasFactory;

    protected $table = 'tb_ppic_paper_meeting_locations';

    protected $fillable = [
        'meeting_id',
        'location_code',
        'location_name',
        'sort_order',
    ];

    protected $casts = [
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
     * Relasi ke stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(PpicPaperMeetingStock::class, 'location_id');
    }
}
