<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HandlingJobPrepress extends Model
{
    use softDeletes, HasFactory;

    protected $table = 'tb_handling_job_prepresses';

    protected $guarded = ['id'];
}
