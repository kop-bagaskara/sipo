<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPurchasing extends Model
{
    use HasFactory;

    protected $table = 'tb_material_purchasing';

    protected $fillable = [
        'job_order_development_id',
        'material_type',
        'material_detail',
        'purchasing_status',
        'purchasing_info',
        'updated_by'
    ];

    protected $casts = [
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job order development that owns the material purchasing.
     */
    public function jobOrderDevelopment()
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_order_development_id');
    }

    /**
     * Get the user who updated the material purchasing.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get material type label
     */
    public function getMaterialTypeLabelAttribute()
    {
        $labels = [
            'kertas' => 'Kertas Khusus',
            'tinta' => 'Tinta Khusus',
            'foil' => 'Foil Khusus',
            'pale_tooling' => 'Pale Tooling Khusus'
        ];

        return $labels[$this->material_type] ?? $this->material_type;
    }

    /**
     * Get purchasing status label
     */
    public function getPurchasingStatusLabelAttribute()
    {
        return $this->purchasing_status === 'sudah' ? 'Sudah Info' : 'Belum Info';
    }

    /**
     * Get material icon
     */
    public function getMaterialIconAttribute()
    {
        $icons = [
            'kertas' => 'mdi-file-document',
            'tinta' => 'mdi-palette',
            'foil' => 'mdi-star',
            'pale_tooling' => 'mdi-hammer'
        ];

        return $icons[$this->material_type] ?? 'mdi-circle';
    }

    /**
     * Get material color class
     */
    public function getMaterialColorClassAttribute()
    {
        $colors = [
            'kertas' => 'text-primary',
            'tinta' => 'text-success',
            'foil' => 'text-warning',
            'pale_tooling' => 'text-danger'
        ];

        return $colors[$this->material_type] ?? 'text-secondary';
    }
}
