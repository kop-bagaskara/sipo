<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationSetting extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';
    protected $table = 'email_notification_settings';

    protected $fillable = [
        'notification_name',
        'notification_type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relasi dengan user yang dapat email
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'email_notification_user_settings', 'email_notification_setting_id', 'user_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    /**
     * Dapatkan semua user aktif yang dapat email untuk notifikasi tertentu
     */
    public function getActiveUsers()
    {
        return $this->users()->wherePivot('is_active', true)->get();
    }

    /**
     * Dapatkan setting berdasarkan tipe notifikasi
     */
    public static function getByType($type)
    {
        return static::where('notification_type', $type)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Dapatkan semua user yang dapat email untuk tipe notifikasi tertentu
     */
    public static function getUsersByType($type)
    {
        $setting = static::getByType($type);
        return $setting ? $setting->getActiveUsers() : collect();
    }

    /**
     * Cek apakah notifikasi aktif berdasarkan tipe
     */
    public static function isActive($notificationType)
    {
        return static::where('notification_type', $notificationType)
                    ->where('is_active', true)
                    ->exists();
    }
}
