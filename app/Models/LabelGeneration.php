<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelGeneration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_label_generations';

    protected $fillable = [
        'template_id',
        'customer_id',
        'field_values',
        'pdf_file_path',
        'pdf_file_name',
        'quantity',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'field_values' => 'array',
        'quantity' => 'integer',
    ];

    /**
     * Get the template that this generation belongs to
     */
    public function template()
    {
        return $this->belongsTo(LabelTemplate::class, 'template_id');
    }

    /**
     * Get the customer that this generation belongs to
     */
    public function customer()
    {
        return $this->belongsTo(LabelCustomer::class, 'customer_id');
    }

    /**
     * Get the user who created this generation
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

