<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForecastItem extends Model
{
    use HasFactory;

    protected $table = 'tb_forecast_items';

    protected $fillable = [
        'forecast_id',
        'material_code',
        'design_code',
        'item_name',
        'remarks',
        'dpc_group',
        'forecast_qty',
        'forecast_ton',
        'ao_qty',
        'ao_ton',
        'sod_qty',
        'sod_ton',
        'sort_order',
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
     * Relasi ke forecast
     */
    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }

    /**
     * Relasi ke weekly data
     */
    public function weeklyData(): HasMany
    {
        return $this->hasMany(ForecastWeeklyData::class, 'forecast_item_id');
    }

    /**
     * Update summary dari weekly data
     */
    public function updateSummary()
    {
        $weeklyData = $this->weeklyData;

        $this->forecast_qty = $weeklyData->sum('forecast_qty') ?? 0;
        $this->forecast_ton = $weeklyData->sum('forecast_ton') ?? 0;
        $this->ao_qty = $weeklyData->sum('ao_qty') ?? 0;
        $this->ao_ton = $weeklyData->sum('ao_ton') ?? 0;
        $this->sod_qty = $weeklyData->sum('sod_qty') ?? 0;
        $this->sod_ton = $weeklyData->sum('sod_ton') ?? 0;

        $this->save();
    }
}

