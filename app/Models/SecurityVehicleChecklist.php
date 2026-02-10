<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SecurityVehicleChecklist extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_security_vehicle_checklists';

    protected $fillable = [
        'no_urut',
        'tanggal',
        'nama_driver',
        'model_kendaraan',
        'jam_out',
        'jam_in',
        'bbm_awal',
        'bbm_akhir',
        'km_awal',
        'km_akhir',
        'tujuan',
        'keterangan',
        'no_polisi',
        'petugas_security',
        'shift',
        'status',
        'checklist_pada',
        'lokasi',
        'foto_kondisi',
        'foto_dashboard',
        'foto_driver',
        'foto_lainnya'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_out' => 'datetime:H:i',
        'jam_in' => 'datetime:H:i',
        'bbm_awal' => 'decimal:2',
        'bbm_akhir' => 'decimal:2'
    ];

    // Accessor untuk format tanggal Indonesia
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
    }

    // Accessor untuk format shift
    public function getShiftFormattedAttribute()
    {
        $shifts = [
            'pagi' => 'Pagi (06:00 - 14:00)',
            'siang' => 'Siang (14:00 - 22:00)',
            'malam' => 'Malam (22:00 - 06:00)'
        ];

        return $shifts[$this->shift] ?? ucfirst($this->shift);
    }

    // Accessor untuk menghitung selisih BBM
    public function getSelisihBbmAttribute()
    {
        if ($this->bbm_awal && $this->bbm_akhir) {
            return $this->bbm_akhir - $this->bbm_awal;
        }
        return null;
    }

    // Accessor untuk menghitung selisih KM
    public function getSelisihKmAttribute()
    {
        if ($this->km_awal && $this->km_akhir) {
            return $this->km_akhir - $this->km_awal;
        }
        return null;
    }

    // Accessor untuk format checklist_pada
    public function getChecklistPadaFormattedAttribute()
    {
        $formats = [
            'awal_masuk' => 'AWAL MASUK',
            'akhir_keluar' => 'AKHIR KELUAR'
        ];

        return $formats[$this->checklist_pada] ?? ucfirst($this->checklist_pada);
    }

    // Accessor untuk format lokasi
    public function getLokasiFormattedAttribute()
    {
        $lokasi = [
            1 => 'Lokasi 19 (KRISANTHIUM)',
            2 => 'Lokasi 23 (KRISANTHIUM)',
            3 => 'Lokasi 15 (BERBEK)'
        ];

        return $lokasi[$this->lokasi] ?? 'Lokasi tidak diketahui';
    }

    // Accessor untuk durasi perjalanan
    public function getDurasiPerjalananAttribute()
    {
        if ($this->jam_out && $this->jam_in) {
            $out = Carbon::parse($this->jam_out);
            $in = Carbon::parse($this->jam_in);

            // Jika jam masuk lebih kecil dari jam keluar, berarti lewat tengah malam
            if ($in->lt($out)) {
                $in->addDay();
            }

            return $out->diff($in)->format('%H:%I');
        }
        return null;
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

    // Scope untuk filter berdasarkan driver
    public function scopeByDriver($query, $driver)
    {
        return $query->where('nama_driver', 'like', '%' . $driver . '%');
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Method untuk generate nomor urut otomatis
    public static function generateNoUrut($tanggal)
    {
        $lastRecord = self::whereDate('tanggal', $tanggal)
            ->orderBy('no_urut', 'desc')
            ->first();

        return $lastRecord ? $lastRecord->no_urut + 1 : 1;
    }

    // Method untuk update status kendaraan masuk
    public function updateKembali($jamMasuk, $kmAkhir, $bbmAkhir, $keterangan = null)
    {
        $this->update([
            'jam_in' => $jamMasuk,
            'km_akhir' => $kmAkhir,
            'bbm_akhir' => $bbmAkhir,
            'keterangan' => $keterangan ? $this->keterangan . ' | ' . $keterangan : $this->keterangan,
            'status' => 'selesai'
        ]);
    }
}
