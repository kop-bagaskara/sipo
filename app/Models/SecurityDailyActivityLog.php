<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SecurityDailyActivityLog extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_security_daily_activity_logs';

    protected $fillable = [
        'tanggal',
        'hari',
        'shift',
        'jam_mulai',
        'jam_selesai',
        'personil_jaga',
        'kondisi_awal',
        'kondisi_akhir',
        'menyerahkan_by',
        'diterima_by',
        'diketahui_by',
        'petugas_security',
        'lokasi',
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    // Relationship dengan activity entries
    public function activityEntries()
    {
        return $this->hasMany(SecurityActivityEntry::class, 'daily_log_id');
    }

    // Accessor untuk format hari
    public function getHariFormattedAttribute()
    {
        $hari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        return $hari[$this->hari] ?? $this->hari;
    }

    // Accessor untuk format shift
    public function getShiftFormattedAttribute()
    {
        $shift = [
            'I' => 'I (Pagi)',
            'II' => 'II (Sore)',
            'III' => 'III (Malam)'
        ];

        return $shift[$this->shift] ?? $this->shift;
    }

    // Accessor untuk format jam mulai
    public function getJamMulaiFormattedAttribute()
    {
        if (!$this->jam_mulai) {
            return '-';
        }

        if (is_string($this->jam_mulai)) {
            return Carbon::parse($this->jam_mulai)->format('H.i');
        }

        return $this->jam_mulai->format('H.i');
    }

    // Accessor untuk format jam selesai
    public function getJamSelesaiFormattedAttribute()
    {
        if (!$this->jam_selesai) {
            return '-';
        }

        if (is_string($this->jam_selesai)) {
            return Carbon::parse($this->jam_selesai)->format('H.i');
        }

        return $this->jam_selesai->format('H.i');
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    // Scope untuk filter berdasarkan shift
    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    // Scope untuk filter berdasarkan personil
    public function scopeByPersonil($query, $personil)
    {
        return $query->where('personil_jaga', 'like', '%' . $personil . '%');
    }

    // Method untuk generate nomor laporan
    public static function generateNoLaporan($tanggal)
    {
        $date = Carbon::parse($tanggal);
        $prefix = 'LAP-' . $date->format('Ymd');

        $lastLog = self::whereDate('tanggal', $tanggal)
                      ->orderBy('id', 'desc')
                      ->first();

        if ($lastLog) {
            $lastNumber = (int) substr($lastLog->no_laporan ?? '', -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
