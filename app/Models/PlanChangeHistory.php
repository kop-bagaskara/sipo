<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanChangeHistory extends Model
{
    protected $table = 'tb_plan_change_histories';

    protected $fillable = [
        'code_plan',
        'old_date',
        'new_date',
        'old_machine',
        'new_machine',
        'change_reason',
        'notes',
        'changed_by'
    ];

    protected $casts = [
        'old_date' => 'date',
        'new_date' => 'date'
    ];

    public function plan()
    {
        return $this->belongsTo(PlanContinuedProduction::class, 'code_plan', 'code_plan');
    }
}
