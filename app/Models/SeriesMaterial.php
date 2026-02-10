<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeriesMaterial extends Model
{
    use hasFactory;

    protected $table = 'tb_series_materials';

    protected $guarded = [
        'id',
    ];
}
