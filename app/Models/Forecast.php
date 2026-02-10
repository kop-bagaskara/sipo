<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forecast extends Model
{
    use HasFactory;

    protected $table = 'tb_forecasts';

    protected $fillable = [
        'forecast_number',
        'customer_name',
        'period_month',
        'period_year',
        'status',
        'created_by',
        'approved_by',
        'notes',
        'submitted_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relasi ke items
     */
    public function items(): HasMany
    {
        return $this->hasMany(ForecastItem::class, 'forecast_id');
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
     * Generate forecast number
     */
    public static function generateForecastNumber($customerName, $periodYear)
    {
        $prefix = 'FC-' . strtoupper(substr($customerName, 0, 3)) . '-' . $periodYear . '-';
        $lastForecast = self::where('forecast_number', 'like', $prefix . '%')
            ->orderBy('forecast_number', 'desc')
            ->first();

        if ($lastForecast) {
            $lastNumber = (int) substr($lastForecast->forecast_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }
}

