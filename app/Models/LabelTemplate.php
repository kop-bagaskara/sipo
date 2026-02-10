<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_label_templates';

    protected $fillable = [
        'customer_id',
        'template_name',
        'template_type', // 'besar' atau 'kecil'
        'brand_name',
        'product_name',
        'file_path',
        'file_name',
        'file_size',
        'description',
        'field_mapping',
        'html_template',
        'css_styles',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'field_mapping' => 'array',
    ];

    /**
     * Get the customer that owns this template
     */
    public function customer()
    {
        return $this->belongsTo(LabelCustomer::class, 'customer_id');
    }

    /**
     * Get the user who created this template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this template
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for template type
     */
    public function scopeType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Scope for brand
     */
    public function scopeBrand($query, $brand)
    {
        return $query->where('brand_name', $brand);
    }

    /**
     * Get all generations for this template
     */
    public function generations()
    {
        return $this->hasMany(LabelGeneration::class, 'template_id');
    }
}

