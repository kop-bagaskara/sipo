<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCodeOperator extends Model
{
    use HasFactory;

    protected $table = 'tb_master_code_operator';

    protected $fillable = [
        'Mesin',
        'Nama',
        'Kode',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

