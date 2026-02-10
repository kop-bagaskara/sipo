<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPrepress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_job_prepresses';

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'nomor_job_order',
    //     'tanggal_job_order',
    //     'tanggal_deadline',
    //     'customer',
    //     'product',
    //     'kode_design',
    //     'dimension',
    //     'material',
    //     'total_color',
    //     'total_color_details',
    //     'qty_order_estimation',
    //     'job_order',
    //     'file_data',
    //     'created_by',
    //     'changed_by',
    //     'deleted_by',
    //     'sub_unit_job'
    // ];

    protected $casts = [
        'tanggal_job_order' => 'datetime',
        'tanggal_deadline' => 'datetime',
        'job_order' => 'string',
        'file_data' => 'string'
    ];

    public function assignJob()
    {
        return $this->belongsTo(AssignJobPrepress::class, 'id', 'id_job_order');
    }

    public function assignJobPrepress()
    {
        return $this->hasOne(AssignJobPrepress::class, 'id_job_order', 'id');
    }
    public function attachmentJobOrder()
    {
        return $this->hasMany(AttachmentJobOrder::class, 'id_job_order', 'id');
    }

    public function handlingJobPrepress()
    {
        return $this->hasMany(HandlingJobPrepress::class, 'id_job_order', 'id');
    }

    // Relationships
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function changed_by_user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function deleted_by_user()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the development job that this prepress job was assigned from.
     */
    public function developmentJob()
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'development_job_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status_job', 'OPEN');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status_job', 'IN_PROGRESS');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_job', 'COMPLETED');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'OPEN' => 'badge-primary',
            'IN_PROGRESS' => 'badge-warning',
            'COMPLETED' => 'badge-success',
            'CANCELLED' => 'badge-danger'
        ];

        return $badges[$this->status_job] ?? 'badge-secondary';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'Urgent' => 'badge-danger',
            'Normal' => 'badge-info'
        ];

        return $badges[$this->prioritas_job] ?? 'badge-secondary';
    }

    public function getJobTypeBadgeAttribute()
    {
        $badges = [
            'new' => 'badge-primary',
            'repeat' => 'badge-info'
        ];

        return $badges[$this->job_type] ?? 'badge-secondary';
    }
}
