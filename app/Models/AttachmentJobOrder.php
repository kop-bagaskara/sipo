<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentJobOrder extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tb_attachment_job_orders';

    protected $guarded = ['id'];
}
