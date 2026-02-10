<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SecurityActivityEntry extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_security_activity_entries';

    protected $fillable = [
        'daily_log_id',
        'urutan',
        'time_in',
        'time_out',
        'keterangan'
    ];

    protected $casts = [
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i'
    ];

    // Relationship dengan daily log
    public function dailyLog()
    {
        return $this->belongsTo(SecurityDailyActivityLog::class, 'daily_log_id');
    }

    // Accessor untuk durasi aktivitas
    public function getDurasiAttribute()
    {
        if ($this->time_in && $this->time_out) {
            $start = Carbon::parse($this->time_in);
            $end = Carbon::parse($this->time_out);
            return $start->diffInMinutes($end);
        }
        return null;
    }

    // Accessor untuk format durasi
    public function getDurasiFormattedAttribute()
    {
        $durasi = $this->durasi;
        if ($durasi) {
            $jam = floor($durasi / 60);
            $menit = $durasi % 60;
            return $jam . 'j ' . $menit . 'm';
        }
        return '-';
    }

    // Accessor untuk format time_in
    public function getTimeInFormattedAttribute()
    {
        if (!$this->time_in) {
            return null;
        }

        if (is_string($this->time_in)) {
            return Carbon::parse($this->time_in)->format('H:i');
        }

        return $this->time_in->format('H:i');
    }

    // Accessor untuk format time_out
    public function getTimeOutFormattedAttribute()
    {
        if (!$this->time_out) {
            return null;
        }

        if (is_string($this->time_out)) {
            return Carbon::parse($this->time_out)->format('H:i');
        }

        return $this->time_out->format('H:i');
    }

    // Scope untuk urutan
    public function scopeByUrutan($query, $urutan)
    {
        return $query->where('urutan', $urutan);
    }

    // Scope untuk aktivitas dengan time_in
    public function scopeWithTimeIn($query)
    {
        return $query->whereNotNull('time_in');
    }

    // Scope untuk aktivitas dengan time_out
    public function scopeWithTimeOut($query)
    {
        return $query->whereNotNull('time_out');
    }
}
