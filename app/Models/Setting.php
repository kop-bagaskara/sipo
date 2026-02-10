<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'master',
        'value'
    ];

    /**
     * Get setting value by master key
     */
    public static function getValue($master, $default = null)
    {
        $setting = self::where('master', $master)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by master key
     */
    public static function setValue($master, $value)
    {
        return self::updateOrCreate(
            ['master' => $master],
            ['value' => $value]
        );
    }

    /**
     * Get active machines setting
     */
    public static function getActiveMachines()
    {
        $value = self::getValue('active_machines_plan', '');
        return $value ? explode(',', $value) : [];
    }

    /**
     * Set active machines setting
     */
    public static function setActiveMachines($machineCodes)
    {
        $value = is_array($machineCodes) ? implode(',', $machineCodes) : $machineCodes;
        return self::setValue('active_machines_plan', $value);
    }
}
