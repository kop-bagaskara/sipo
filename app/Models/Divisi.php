<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory as FactoriesHasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Divisi extends Model
{
    use SoftDeletes, FactoriesHasFactory;
    protected $connection = 'pgsql';
    protected $table = 'tb_divisis';
    protected $guarded = ['id'];

    // Relations
    public function approvalSetting()
    {
        return $this->hasOne(DivisiApprovalSetting::class, 'divisi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'divisi');
    }
}
