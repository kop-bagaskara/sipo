<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMachine extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'mastermachine';

    protected $fillable = [
        'Code',
        'Description',
        'Department',
        'Unit',
        'CapacityPerHour',
    ];

    /**
     * Get active machines (excluding those with "JANGAN DIPAKAI" in description)
     */
    public function scopeActive($query)
    {
        return $query->where('Description', 'not like', '%JANGAN DIPAKAI%');
    }

    /**
     * Get machines for dropdown
     */
    public static function getForDropdown()
    {
        return static::active()
            ->orderBy('Code')
            ->get(['Code', 'Description']);
    }
}

