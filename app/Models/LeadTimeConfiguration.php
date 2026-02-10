<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTimeConfiguration extends Model
{
    use HasFactory;

    protected $table = 'tb_lead_time_configurations';

    protected $fillable = [
        'job_order_development_id',
        'tinta_material_days',
        'kertas_baru_days',
        'foil_days',
        'tooling_days',
        'produksi_hours',
        'total_lead_time_days',
        'created_by'
    ];

    protected $casts = [
        'produksi_hours' => 'decimal:2',
        'tinta_material_days' => 'integer',
        'kertas_baru_days' => 'integer',
        'foil_days' => 'integer',
        'tooling_days' => 'integer',
        'total_lead_time_days' => 'integer'
    ];

    public function jobOrderDevelopment()
    {
        return $this->belongsTo(JobOrderDevelopment::class);
    }
}
