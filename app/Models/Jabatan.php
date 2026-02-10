<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'tb_jabatans';
    protected $guarded = ['id'];

    public function divisi()
    {
        return $this->belongsTo('App\Models\Divisi');
    }
}
