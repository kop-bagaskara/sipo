<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDataPrepress extends Model
{
    use SoftDeletes, hasFactory;
    protected $table = 'tb_master_data_prepresses';
    protected $guarded = ['id'];

}
