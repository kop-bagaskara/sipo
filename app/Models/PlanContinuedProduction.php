<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanContinuedProduction extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tb_plan_continued_productions';
    protected $guarded = ['id'];

}
