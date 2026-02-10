<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobOrderDevelopment extends Model
{
    use HasFactory;

    protected $table = 'tb_job_order_developments';

    protected $fillable = [
        'job_code',
        'job_name',
        'tanggal',
        'job_deadline',
        'customer',
        'product',
        'kode_design',
        'dimension',
        'material',
        'total_color',
        'colors',
        'qty_order_estimation',
        'job_type',
        'change_percentage',
        'change_details',
        'job_order',
        'file_data',
        'prioritas_job',
        'attachment_paths',
        'catatan',
        'status_job',
        'marketing_user_id',
        'assigned_to_ppic_at',
        'assigned_to_ppic_by',
        'progress_notes',
        'started_at',
        'completed_at',
        // Special materials fields
        'kertas_khusus',
        'kertas_khusus_detail',
        'tinta_khusus',
        'tinta_khusus_detail',
        'foil_khusus',
        'foil_khusus_detail',
        'pale_tooling_khusus',
        'pale_tooling_khusus_detail',
        'proses',
        'rnd_customer_approval',
        'rnd_customer_notes',
        'rnd_customer_approved_at',
        'rnd_customer_approved_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'job_deadline' => 'date',
        'colors' => 'array',
        'change_details' => 'array',
        'job_order' => 'array',
        'file_data' => 'array',
        'attachment_paths' => 'array',
        'proses' => 'array',
        'change_percentage' => 'integer',
        'assigned_to_ppic_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the marketing user that owns the job order development.
     */
    public function marketingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketing_user_id');
    }

    /**
     * Get the user who assigned this job to PPIC.
     */
    public function assignedToPPICBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_ppic_by');
    }

    /**
     * Get the prepress job that was created from this development job.
     */
    public function prepressJob()
    {
        return $this->hasOne(JobPrepress::class, 'development_job_id');
    }

    /**
     * Get the meeting OPP 1 for this development job.
     */
    public function meetingOpp1()
    {
        return $this->hasOne(MeetingOPP::class, 'job_development_id')->where('meeting_number', 1);
    }

    /**
     * Get the meeting OPP 2 for this development job.
     */
    public function meetingOpp2()
    {
        return $this->hasOne(MeetingOPP::class, 'job_development_id')->where('meeting_number', 2);
    }

    /**
     * Get the scheduling development for this job.
     */
    public function schedulingDevelopment()
    {
        return $this->hasOne(SchedulingDevelopment::class, 'job_development_id');
    }

    /**
     * Get the map proof for this job.
     */
    public function mapProof()
    {
        return $this->hasOne(MapProof::class, 'job_development_id');
    }

    /**
     * Get the sales order for this job.
     */
    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class, 'job_development_id');
    }

    /**
     * Get the material purchasing records for this job.
     */
    public function materialPurchasing()
    {
        return $this->hasMany(MaterialPurchasing::class, 'job_order_development_id');
    }

    /**
     * Get the production schedules for this job.
     */
    public function productionSchedules()
    {
        return $this->hasMany(ProductionSchedule::class, 'job_order_development_id');
    }

    public function leadTimeConfiguration()
    {
        return $this->hasOne(LeadTimeConfiguration::class, 'job_order_development_id');
    }
}
