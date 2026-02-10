<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignJobPrepress extends Model
{
    use SoftDeletes, hasFactory;
    protected $table = 'tb_assign_job_prepresses';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_pic', 'id');
    }
}
