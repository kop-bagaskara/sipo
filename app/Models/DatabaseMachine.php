<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatabaseMachine extends Model
{
    use hasFactory, SoftDeletes;

    protected $table = 'tb_database_machines';

    protected $guarded = [
        'id',
    ];
}
