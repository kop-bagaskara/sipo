<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AssetUsageLog extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_asset_usage_logs';

    protected $fillable = [
        'request_id',
        'asset_type',
        'asset_id',
        'employee_id',
        'usage_date',
        'return_date',
        'usage_purpose',
        'status'
    ];

    protected $casts = [
        'usage_date' => 'date',
        'return_date' => 'date'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';

    /**
     * Get the request that owns this usage log
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(EmployeeRequest::class, 'request_id');
    }

    /**
     * Get the employee who borrowed the asset
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the vehicle (if asset_type is vehicle)
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(CompanyVehicle::class, 'asset_id')
                   ->where('asset_type', 'vehicle');
    }

    /**
     * Get the asset (if asset_type is inventory)
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id')
                   ->where('asset_type', 'inventory');
    }

    /**
     * Scope for active usage
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for overdue usage
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }

    /**
     * Scope for returned assets
     */
    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    /**
     * Check if usage is overdue
     */
    public function isOverdue()
    {
        return $this->status === self::STATUS_OVERDUE ||
               ($this->status === self::STATUS_ACTIVE &&
                $this->usage_date < now()->subDays(1) &&
                (!$this->return_date || $this->return_date < now()));
    }

    /**
     * Get usage duration in days
     */
    public function getDurationDaysAttribute()
    {
        $endDate = $this->return_date ?: now();
        return $this->usage_date->diffInDays($endDate);
    }

    /**
     * Get asset name based on type
     */
    public function getAssetNameAttribute()
    {
        if ($this->asset_type === 'vehicle') {
            return $this->vehicle ? $this->vehicle->full_name : 'Unknown Vehicle';
        } else {
            return $this->asset ? $this->asset->full_name : 'Unknown Asset';
        }
    }

    /**
     * Update status based on current date
     */
    public function updateStatus()
    {
        if ($this->status === self::STATUS_RETURNED) {
            return; // Already returned
        }

        if ($this->isOverdue()) {
            $this->update(['status' => self::STATUS_OVERDUE]);
        } else {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }
}
