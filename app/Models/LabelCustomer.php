<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_label_customers';

    protected $fillable = [
        'customer_code',
        'customer_name',
        'brand_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this customer
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this customer
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get templates for this customer
     */
    public function templates()
    {
        return $this->hasMany(LabelTemplate::class, 'customer_id');
    }

    /**
     * Get all label generations for this customer
     */
    public function generations()
    {
        return $this->hasMany(LabelGeneration::class, 'customer_id');
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

