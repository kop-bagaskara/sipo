<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeEntry extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2'; // HR DB
    protected $table = 'tb_overtime_entries';

    protected $fillable = [
        'request_date',       // date (hari/tanggal)
        'location',           // lokasi
        'employee_id',        // optional link to users
        'employee_name',      // nama karyawan (denormalized)
        'department',         // bagian
        'start_time',
        'end_time',
        'job_description',    // keterangan pekerjaan
        'divisi_id',          // for filtering per divisi
        'status',
        'spv_id', 'spv_notes', 'spv_at',
        'head_id', 'head_notes', 'head_at',
        'hrga_id', 'hrga_notes', 'hrga_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'spv_at' => 'datetime',
        'head_at' => 'datetime',
        'hrga_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_PENDING_SPV = 'pending_spv';
    public const STATUS_SPV_APPROVED = 'spv_approved';
    public const STATUS_SPV_REJECTED = 'spv_rejected';
    public const STATUS_HEAD_APPROVED = 'head_approved';
    public const STATUS_HEAD_REJECTED = 'head_rejected';
    public const STATUS_HRGA_APPROVED = 'hrga_approved';
    public const STATUS_HRGA_REJECTED = 'hrga_rejected';

    public function scopeForDivisi($query, int $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopePendingSpv($query)
    {
        return $query->where('status', self::STATUS_PENDING_SPV);
    }

    public function scopePendingHead($query)
    {
        return $query->where('status', self::STATUS_SPV_APPROVED);
    }

    public function scopePendingHrga($query)
    {
        return $query->where('status', self::STATUS_HEAD_APPROVED);
    }

    // Accessors to User on default pgsql (cross-DB safe: use find by id)
    public function getSpvAttribute()
    {
        return $this->spv_id ? \App\Models\User::find($this->spv_id) : null;
    }

    public function getHeadAttribute()
    {
        return $this->head_id ? \App\Models\User::find($this->head_id) : null;
    }

    public function getHrgaAttribute()
    {
        return $this->hrga_id ? \App\Models\User::find($this->hrga_id) : null;
    }
}
