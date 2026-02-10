<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayDays extends Model
{
    protected $table = 'tb_holiday_days';
    protected $fillable = [
        'date',
        'override_type',
        'working_hours',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'working_hours' => 'decimal:2'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
