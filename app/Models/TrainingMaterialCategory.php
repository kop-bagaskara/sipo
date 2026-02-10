<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingMaterialCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_material_categories';

    protected $fillable = [
        'category_name',
        'description',
        'display_order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get materials in this category
     */
    public function materials()
    {
        return $this->hasMany(TrainingMaterial::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}

