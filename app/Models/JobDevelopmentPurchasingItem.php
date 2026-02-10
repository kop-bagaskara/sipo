<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDevelopmentPurchasingItem extends Model
{
    use HasFactory;

    protected $table = 'tb_job_development_purchasing_items';

    protected $fillable = [
        'job_development_id',
        'item_name',
        'supplier_name',
        'order_status',
        'order_date',
        'received_date',
        'notes',
        'purchasing_user_id'
    ];

    protected $casts = [
        'order_date' => 'date',
        'received_date' => 'date'
    ];

    // Relationships
    public function jobDevelopment()
    {
        return $this->belongsTo(JobDevelopment::class, 'job_development_id');
    }

    public function purchasingUser()
    {
        return $this->belongsTo(User::class, 'purchasing_user_id');
    }
}
