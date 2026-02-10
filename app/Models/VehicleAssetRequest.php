<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleAssetRequest extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_vehicle_asset_requests';

    protected $fillable = [
        'request_date',
        'request_type', // 'vehicle' or 'asset'
        'employee_id',
        'employee_name',
        'department',
        'divisi_id',
        'vehicle_type', // for vehicle requests
        'asset_category', // for asset requests
        'purpose_type', // Meeting, Dinas Luar, Training, etc
        'purpose',
        'destination', // Tujuan penggunaan
        'license_plate', // No. Polisi
        'start_date',
        'end_date',
        'notes',
        'status',
        'manager_id',
        'manager_notes',
        'manager_at',
        'general_id',
        'general_approved_at',
        'general_rejected_at',
        'general_notes',
        'hrga_id',
        'hrga_notes',
        'hrga_at',
        'created_at',
        'updated_at',
        'request_number'
    ];

    protected $casts = [
        'request_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'manager_at' => 'datetime',
        'general_approved_at' => 'datetime',
        'general_rejected_at' => 'datetime',
        'hrga_at' => 'datetime',
        'request_number' => 'string'
    ];

    // Status constants
    const STATUS_PENDING_MANAGER = 'pending_manager';
    const STATUS_MANAGER_APPROVED = 'manager_approved';
    const STATUS_MANAGER_REJECTED = 'manager_rejected';
    const STATUS_HRGA_APPROVED = 'hrga_approved';
    const STATUS_HRGA_REJECTED = 'hrga_rejected';

    // Request type constants
    const TYPE_VEHICLE = 'vehicle';
    const TYPE_ASSET = 'asset';

    // Scopes
    public function scopeForDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopePendingManager($query)
    {
        return $query->where('status', self::STATUS_PENDING_MANAGER);
    }

    public function scopePendingHrga($query)
    {
        return $query->where('status', self::STATUS_MANAGER_APPROVED);
    }

    public function scopeApprovedHrga($query)
    {
        return $query->where('status', self::STATUS_HRGA_APPROVED);
    }

    // Accessor methods for cross-database relations
    public function getManagerAttribute()
    {
        if ($this->manager_id) {
            return \App\Models\User::find($this->manager_id);
        }
        return null;
    }

    public function getHrgaAttribute()
    {
        if ($this->hrga_id) {
            return \App\Models\User::find($this->hrga_id);
        }
        return null;
    }

    // Helper methods
    public function isPendingManager()
    {
        return $this->status === self::STATUS_PENDING_MANAGER;
    }

    public function isManagerApproved()
    {
        return $this->status === self::STATUS_MANAGER_APPROVED;
    }

    public function isHrgaApproved()
    {
        return $this->status === self::STATUS_HRGA_APPROVED;
    }

    public function isVehicleRequest()
    {
        return $this->request_type === self::TYPE_VEHICLE;
    }

    public function isAssetRequest()
    {
        return $this->request_type === self::TYPE_ASSET;
    }
}
