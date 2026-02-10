<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SecurityGoodsMovement extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_security_goods_movements';

    protected $fillable = [
        'no_urut',
        'tanggal',
        'nama_pengunjung',
        'alamat',
        'no_telepon',
        'perusahaan_asal',
        'jenis_movement',
        'barang_items', // New JSON field for multiple items
        'barang_keluar_items', // New JSON field for barang keluar items
        'jenis_barang', // Keep for backward compatibility
        'deskripsi_barang',
        'jumlah',
        'satuan',
        'berat',
        'jam_masuk',
        'jam_keluar',
        'status_laporan',
        'tujuan',
        'asal',
        'jenis_kendaraan',
        'no_polisi',
        'nama_driver',
        'no_surat_jalan',
        'no_invoice',
        'dokumen_pendukung',
        'petugas_security',
        'shift',
        'keterangan',
        'catatan_security',
        'lokasi'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'berat' => 'decimal:2',
        'barang_items' => 'array', // Cast JSON to array
        'barang_keluar_items' => 'array' // Cast JSON to array
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

    // Accessor untuk durasi di lokasi (jika ada jam masuk dan keluar)
    public function getDurasiAttribute()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = Carbon::parse($this->jam_masuk);
            $keluar = Carbon::parse($this->jam_keluar);

            // Jika jam keluar lebih kecil dari jam masuk, berarti lewat tengah malam
            if ($keluar->lt($masuk)) {
                $keluar->addDay();
            }

            return $masuk->diff($keluar)->format('%H:%I');
        }
        return null;
    }


    // Accessor untuk jenis movement badge color
    public function getMovementColorAttribute()
    {
        switch($this->jenis_movement) {
            case 'masuk':
                return 'success';
            case 'keluar':
                return 'primary';
            default:
                return 'secondary';
        }
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

    // Scope untuk filter berdasarkan jenis movement
    public function scopeByMovement($query, $movement)
    {
        return $query->where('jenis_movement', $movement);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan nama pengunjung
    public function scopeByVisitor($query, $visitor)
    {
        return $query->where('nama_pengunjung', 'like', '%' . $visitor . '%');
    }

    // Scope untuk filter berdasarkan jenis barang
    public function scopeByGoods($query, $goods)
    {
        return $query->where('jenis_barang', 'like', '%' . $goods . '%');
    }

    // Method untuk generate nomor urut otomatis
    public static function generateNoUrut($tanggal)
    {
        $lastRecord = self::whereDate('tanggal', $tanggal)
            ->orderBy('no_urut', 'desc')
            ->first();

        return $lastRecord ? $lastRecord->no_urut + 1 : 1;
    }


    // Helper methods untuk barang items
    public function getBarangItemsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    public function setBarangItemsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['barang_items'] = json_encode($value);
        } else {
            $this->attributes['barang_items'] = $value;
        }
    }

    // Method untuk get total jumlah barang
    public function getTotalJumlahBarangAttribute()
    {
        $total = 0;
        foreach ($this->barang_items as $item) {
            $total += $item['jumlah'] ?? 0;
        }
        return $total;
    }

    // Method untuk get total berat barang
    public function getTotalBeratBarangAttribute()
    {
        $total = 0;
        foreach ($this->barang_items as $item) {
            $total += $item['berat'] ?? 0;
        }
        return $total;
    }

    // Helper methods untuk barang keluar items
    public function getBarangKeluarItemsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    public function setBarangKeluarItemsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['barang_keluar_items'] = json_encode($value);
        } else {
            $this->attributes['barang_keluar_items'] = $value;
        }
    }

    // Method untuk get total jumlah barang keluar
    public function getTotalJumlahBarangKeluarAttribute()
    {
        $total = 0;
        foreach ($this->barang_keluar_items as $item) {
            $total += $item['jumlah'] ?? 0;
        }
        return $total;
    }

    // Method untuk get total berat barang keluar
    public function getTotalBeratBarangKeluarAttribute()
    {
        $total = 0;
        foreach ($this->barang_keluar_items as $item) {
            $total += $item['berat'] ?? 0;
        }
        return $total;
    }

    // Method untuk get summary statistics
    public static function getSummaryStats($date = null)
    {
        $query = self::query();

        if ($date) {
            $query->whereDate('tanggal', $date);
        } else {
            $query->whereDate('tanggal', today());
        }

        return [
            'total' => $query->count(),
            'masuk' => $query->clone()->where('jenis_movement', 'masuk')->count(),
            'keluar' => $query->clone()->where('jenis_movement', 'keluar')->count(),
        ];
    }
}
