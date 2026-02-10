<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionSchedule extends Model
{
    use HasFactory;

    protected $table = 'tb_production_schedules';

    protected $fillable = [
        'job_order_development_id',
        'production_date',
        'production_time',
        'machine_name',
        'machine_code',
        'status',
        'production_notes',
        'quality_notes',
        'production_qty',
        'reject_qty',
        'start_time',
        'end_time',
        'completion_date',
        'issues_found',
        'recommendations',
        'rnd_approval_status',
        'rnd_approval_notes',
        'rnd_approved_by',
        'rnd_approved_at',
        'revision_count',
        'created_by',
        'proses'
    ];

    protected $casts = [
        'production_date' => 'date',
        'production_time' => 'datetime:H:i',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'completion_date' => 'date',
        'rnd_approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function jobOrderDevelopment()
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_order_development_id');
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rndApprovedBy()
    {
        return $this->belongsTo(User::class, 'rnd_approved_by');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'scheduled' => 'Terjadwal',
            'ready' => 'Siap',
            'in_progress' => 'Sedang Produksi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorClassAttribute()
    {
        return [
            'scheduled' => 'badge-primary',
            'ready' => 'badge-info',
            'in_progress' => 'badge-warning',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ][$this->status] ?? 'badge-secondary';
    }

    public function getProductionDateTimeAttribute()
    {
        return $this->production_date->format('d/m/Y') . ' ' . $this->production_time->format('H:i');
    }

    public function getRndApprovalStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui RnD',
            'rejected' => 'Ditolak RnD',
        ][$this->rnd_approval_status] ?? $this->rnd_approval_status;
    }

    public function getRndApprovalStatusColorClassAttribute()
    {
        return [
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
        ][$this->rnd_approval_status] ?? 'badge-secondary';
    }

    public function canBeRevised()
    {
        return $this->rnd_approval_status === 'pending' || $this->rnd_approval_status === 'rejected';
    }
}
