<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default settings untuk Job Order Prepress
        $defaultSettings = [
            [
                'notification_type' => 'job_order_prepress',
                'target_type' => 'divisi',
                'target_value' => 'PPIC',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk divisi PPIC saat ada job order prepress baru',
                'is_active' => true
            ],
            [
                'notification_type' => 'job_order_prepress',
                'target_type' => 'divisi',
                'target_value' => 'PREPRESS',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk divisi PREPRESS saat ada job order prepress baru',
                'is_active' => true
            ],
            [
                'notification_type' => 'job_order_prepress',
                'target_type' => 'jabatan',
                'target_value' => 'HEAD',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk semua HEAD divisi saat ada job order prepress baru',
                'is_active' => true
            ],
            [
                'notification_type' => 'job_order_prepress',
                'target_type' => 'jabatan',
                'target_value' => 'SPV',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk semua SPV divisi saat ada job order prepress baru',
                'is_active' => true
            ],
            [
                'notification_type' => 'job_order_production',
                'target_type' => 'divisi',
                'target_value' => 'PPIC',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk divisi PPIC saat ada job order production baru',
                'is_active' => true
            ],
            [
                'notification_type' => 'job_order_production',
                'target_type' => 'divisi',
                'target_value' => 'PRODUCTION',
                'send_email' => true,
                'send_website' => true,
                'description' => 'Notifikasi untuk divisi PRODUCTION saat ada job order production baru',
                'is_active' => true
            ]
        ];

        foreach ($defaultSettings as $setting) {
            NotificationSetting::updateOrCreate(
                [
                    'notification_type' => $setting['notification_type'],
                    'target_type' => $setting['target_type'],
                    'target_value' => $setting['target_value']
                ],
                $setting
            );
        }

        $this->command->info('Notification settings seeded successfully!');
    }
}
