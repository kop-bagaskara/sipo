<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentEmailNotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'tb_development_email_notification_settings';

    protected $fillable = [
        'process_name',
        'process_code',
        'description',
        'recipient_roles',
        'reminder_schedule',
        'is_active',
        'send_to_rnd_on_every_change'
    ];

    protected $casts = [
        'recipient_roles' => 'array',
        'reminder_schedule' => 'array',
        'is_active' => 'boolean',
        'send_to_rnd_on_every_change' => 'boolean'
    ];

    /**
     * Relationship dengan users melalui pivot table
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_development_email_notification_settings', 'setting_id', 'user_id');
    }

    /**
     * Dapatkan setting berdasarkan kode proses
     */
    public static function getByProcessCode($processCode)
    {
        return static::where('process_code', $processCode)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Dapatkan semua user berdasarkan role yang ditentukan
     */
    public function getRecipientsByRoles()
    {
        $roles = $this->recipient_roles;
        $users = collect();

        foreach ($roles as $role) {
            $roleUsers = User::where('divisi', $role)
                           ->orWhere('jabatan', $role)
                           ->orWhere('name', 'like', '%' . $role . '%')
                           ->get();
            $users = $users->merge($roleUsers);
        }

        return $users->unique('id');
    }

    /**
     * Dapatkan user untuk reminder berdasarkan schedule
     */
    public function getReminderRecipients($daysBefore)
    {
        if (!$this->reminder_schedule) {
            return collect();
        }

        $reminderRoles = $this->reminder_schedule[$daysBefore] ?? [];
        $users = collect();

        foreach ($reminderRoles as $role) {
            $roleUsers = User::where('divisi', $role)
                           ->orWhere('jabatan', $role)
                           ->orWhere('name', 'like', '%' . $role . '%')
                           ->get();
            $users = $users->merge($roleUsers);
        }

        return $users->unique('id');
    }

    /**
     * Dapatkan semua user RnD untuk notifikasi setiap perubahan
     */
    public static function getRnDUsers()
    {
        return User::whereIn('divisi', ['RnD', 'Research & Development', 'Quality Control', 'QUALITY'])
                  ->orWhereIn('jabatan', ['RnD', 'Research & Development', 'Quality Control'])
                  ->get();
    }
}
