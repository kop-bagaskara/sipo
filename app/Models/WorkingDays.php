<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkingDays extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'tb_working_days';
    protected $fillable = [
        'day_of_week',
        'day_name',
        'is_working_day',
        'working_hours',
        'is_half_day',
        'half_day_hours',
        'is_active',
        'description'
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
        'is_half_day' => 'boolean',
        'is_active' => 'boolean',
        'working_hours' => 'decimal:2',
        'half_day_hours' => 'decimal:2'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
