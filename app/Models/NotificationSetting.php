<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type',
        'target_type',
        'target_value',
        'is_active',
        'send_email',
        'send_website',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'send_email' => 'boolean',
        'send_website' => 'boolean'
    ];

    /**
     * Dapatkan pengaturan notifikasi berdasarkan tipe
     */
    public static function getSettingsByType($type)
    {
        return static::where('notification_type', $type)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Dapatkan pengaturan untuk job order prepress
     */
    public static function getJobOrderPrepressSettings()
    {
        return static::getSettingsByType('job_order_prepress');
    }

    /**
     * Dapatkan pengaturan untuk job order production
     */
    public static function getJobOrderProductionSettings()
    {
        return static::getSettingsByType('job_order_production');
    }
}
