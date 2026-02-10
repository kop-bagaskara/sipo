<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPekerjaanPrepress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_jenis_pekerjaan_prepresses';

    protected $fillable = [
        'kode',
        'nama_jenis',
        'keterangan',
        'waktu_estimasi',
        'job_rate',
        'point_job',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'waktu_estimasi' => 'integer',
        'job_rate' => 'decimal:2',
        'point_job' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scope untuk data yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationship dengan JobPrepress (jika diperlukan)
    // public function jobPrepresses()
    // {
    //     return $this->hasMany(JobPrepress::class, 'id', 'id');
    // }
}
