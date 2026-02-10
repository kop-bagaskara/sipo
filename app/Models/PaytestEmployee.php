<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PaytestEmployee extends Model
{
    use HasFactory;

    protected $connection = 'mysql7'; // Database paytest
    protected $table = 'masteremployee'; // Tabel masteremployee
    protected $primaryKey = 'Nip'; // Primary key adalah Nip
    
    protected $fillable = [
        'Nip',
        'Nama',
        'Alamat',
        'No Telp',
        'Pengalaman',
        'Tempat Lahir',
        'Tgl Lahir',
        'Agama',
        'Jenis Kelamin',
        'Status Nikah',
        'Jumlah Anak',
        'Status PPh21',
        'Pendidikan',
        'Kode Divisi',
        'Kode Bagian',
        'Kode Jabatan',
        'Kode Group',
        'Kode Admin',
        'Kode Kontrak',
        'Sales Office',
        'Kode Periode',
        'Tgl Masuk',
        'Tgl Keluar',
        'Alasan Keluar',
        'Aktif',
        'Gaji per Bulan',
        'Jumlah SP',
        'No KPJ',
        'No HLD',
        'SPSI',
        'Koperasi',
        'No Rekening',
        'Jari Bermasalah',
        'Catatan',
        'NPWP',
        'Email',
        'Begda',
        'Endda',
        'CreatedBy',
        'CreatedDate',
        'ChangedBy',
        'ChangedDate',
        'User'
    ];

    protected $casts = [
        'Tgl Lahir' => 'date',
        'Tgl Masuk' => 'date',
        'Tgl Keluar' => 'date',
        'Begda' => 'date',
        'Endda' => 'date',
        'CreatedDate' => 'datetime',
        'ChangedDate' => 'datetime',
        'Aktif' => 'boolean',
        'SPSI' => 'boolean',
        'Koperasi' => 'boolean',
        'Jari Bermasalah' => 'boolean',
        'Jumlah Anak' => 'integer',
        'Gaji per Bulan' => 'decimal:2',
        'Jumlah SP' => 'integer'
    ];

    /**
     * Get training participants for this employee
     */
    public function trainingParticipants()
    {
        return $this->hasMany(TrainingParticipant::class, 'employee_id', 'Nip');
    }

    /**
     * Scope for active employees based on Begda and Endda
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('Begda', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->whereNull('Endda')
                          ->orWhere('Endda', '>=', $today);
                    });
    }

    /**
     * Scope for employees by division
     */
    public function scopeByDivision($query, $division)
    {
        return $query->where('Kode Divisi', $division);
    }

    /**
     * Scope for employees by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('Kode Bagian', $department);
    }

    /**
     * Scope for employees by position
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('Kode Jabatan', $position);
    }

    /**
     * Check if employee is currently active
     */
    public function isActive()
    {
        $today = Carbon::today();
        return $this->Begda <= $today && 
               ($this->Endda === null || $this->Endda >= $today);
    }

    /**
     * Get employee's full name
     */
    public function getFullNameAttribute()
    {
        return $this->Nama;
    }

    /**
     * Get employee's email
     */
    public function getEmailAttribute()
    {
        return $this->attributes['Email'] ?? null;
    }

    /**
     * Get employee's department
     */
    public function getDepartmentAttribute()
    {
        return $this->attributes['Kode Bagian'] ?? null;
    }

    /**
     * Get employee's position
     */
    public function getPositionAttribute()
    {
        return $this->attributes['Kode Jabatan'] ?? null;
    }

    /**
     * Get employee's division
     */
    public function getDivisionAttribute()
    {
        return $this->attributes['Kode Divisi'] ?? null;
    }
}
