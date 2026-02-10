<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineShift extends Model
{
    use HasFactory;

    protected $table = 'tb_machine_shift';

    protected $fillable = [
        'machine',
        'shift'
    ];

    /**
     * Get shift configuration for a machine
     */
    public static function getShift($machineCode)
    {
        $config = self::where('machine', $machineCode)->first();
        return $config ? (int)$config->shift : 3; // Default 3 shifts
    }

    /**
     * Set shift configuration for a machine
     */
    public static function setShift($machineCode, $numShifts)
    {
        return self::updateOrCreate(
            ['machine' => $machineCode],
            ['shift' => $numShifts]
        );
    }

    /**
     * Get all machine shift configurations
     */
    public static function getAllShifts()
    {
        return self::pluck('shift', 'machine')->toArray();
    }
}

