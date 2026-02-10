<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrder extends Model
{
    use HasFactory;

    protected $table = 'tb_sales_orders';

    protected $fillable = [
        'job_development_id',
        'order_number',
        'order_date',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'created_by'
    ];

    protected $casts = [
        'order_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job development that owns this sales order.
     */
    public function jobDevelopment(): BelongsTo
    {
        return $this->belongsTo(JobOrderDevelopment::class, 'job_development_id');
    }

    /**
     * Get the user who created this sales order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
