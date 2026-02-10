<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDivisi extends Model
{
    use HasFactory;

    protected $connection = 'mysql7';
    protected $table = 'masterdivisi';

    protected $fillable = [
        'Kode',
        'Nama',
        'Begda',
        'Endda',
        'CreatedBy',
        'CreatedDate',
        'ChangedBy',
        'ChangedDate'
    ];

    protected $casts = [
        'Begda' => 'date',
        'Endda' => 'date',
        'CreatedDate' => 'datetime',
        'ChangedDate' => 'datetime'
    ];

    /**
     * Scope for active divisions based on Begda and Endda
     */
    public function scopeActive($query)
    {
        $today = now();
        return $query->where('Begda', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->whereNull('Endda')
                          ->orWhere('Endda', '>=', $today);
                    });
    }

    /**
     * Check if division is currently active
     */
    public function isActive()
    {
        $today = now();
        return $this->Begda <= $today &&
               ($this->Endda === null || $this->Endda >= $today);
    }
}
