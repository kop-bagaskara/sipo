<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeData extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_employee_data';

    protected $fillable = [
        'nip',
        'nama_karyawan',
        'lp',
        'lvl',
        'dept',
        'bagian',
        'tgl_masuk',
        'status_update',
        'tanggal_awal',
        'tanggal_berakhir',
        'masa_kerja',
        'tempat_lahir',
        'tgl_lahir',
        'usia',
        'alamat_ktp',
        'email',
        'no_hp',
        'alamat_domisili',
        'nomor_kontak_darurat',
        'agama',
        'pendidikan',
        'jurusan',
        'foto_path',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tanggal_awal' => 'date',
        'tanggal_berakhir' => 'date',
        'tgl_lahir' => 'date',
        'usia' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where(function($q) use ($today) {
            $q->whereNull('tanggal_berakhir')
              ->orWhere('tanggal_berakhir', '>=', $today);
        });
    }

    /**
     * Scope for employees by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('dept', $department);
    }

    /**
     * Scope for employees by bagian
     */
    public function scopeByBagian($query, $bagian)
    {
        return $query->where('bagian', $bagian);
    }

    /**
     * Scope for employees by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('lvl', $level);
    }

    /**
     * Calculate usia from tgl_lahir
     */
    public function calculateUsia()
    {
        if ($this->tgl_lahir) {
            return Carbon::parse($this->tgl_lahir)->age;
        }
        return null;
    }

    /**
     * Calculate masa kerja from tgl_masuk
     */
    public function calculateMasaKerja()
    {
        if ($this->tgl_masuk) {
            $masaKerja = Carbon::parse($this->tgl_masuk)->diffInYears(Carbon::now());
            return $masaKerja . ' tahun';
        }
        return null;
    }

    /**
     * Check if employee is currently active
     */
    public function isActive()
    {
        $today = Carbon::today();
        return $this->tanggal_berakhir === null || $this->tanggal_berakhir >= $today;
    }
}

