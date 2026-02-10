<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForecastWeeklyData extends Model
{
    use HasFactory;

    protected $table = 'tb_forecast_weekly_data';

    protected $fillable = [
        'forecast_item_id',
        'week_number',
        'year',
        'week_label',
        'forecast_qty',
        'forecast_ton',
        'ao_qty',
        'ao_ton',
        'sod_qty',
        'sod_ton',
    ];

    protected $casts = [
        'forecast_qty' => 'decimal:2',
        'forecast_ton' => 'decimal:4',
        'ao_qty' => 'decimal:2',
        'ao_ton' => 'decimal:4',
        'sod_qty' => 'decimal:2',
        'sod_ton' => 'decimal:4',
    ];

    /**
     * Relasi ke forecast item
     */
    public function forecastItem(): BelongsTo
    {
        return $this->belongsTo(ForecastItem::class, 'forecast_item_id');
    }

    /**
     * Generate week label
     */
    public static function generateWeekLabel($weekNumber, $year)
    {
        return 'W' . $weekNumber . '.' . $year;
    }
}

