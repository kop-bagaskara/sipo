<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItemUnilever extends Model
{
    use HasFactory;

    protected $connection = 'pgsql'; // PostgreSQL connection
    protected $table = 'tb_master_item_unilever';

    protected $fillable = [
        'KodeDesign',
        'NamaItem',
        'PC',
        'MC',
        'QTY',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

