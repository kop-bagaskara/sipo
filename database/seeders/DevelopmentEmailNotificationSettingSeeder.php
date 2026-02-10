<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DevelopmentEmailNotificationSetting;

class DevelopmentEmailNotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Setting untuk Input Awal Development
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'input_awal',
            'process_name' => 'Input Awal Development',
            'description' => 'Notifikasi saat job development baru diinput oleh marketing',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => 'first',
                    'description' => 'Awal Inputan',
                    'users' => [1] // User ID 1 (Marketing)
                ]
            ]
        ]);

        // Setting untuk Prepress Process
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'prepress',
            'process_name' => 'Job Prepress',
            'description' => 'Notifikasi untuk job prepress dan reminder deadline',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => 'first',
                    'description' => 'Job Dikirim ke Prepress',
                    'users' => [1] // User ID 1 (Marketing)
                ],
                [
                    'days' => '2',
                    'description' => 'H-2',
                    'users' => [1] // User ID 1 (Marketing)
                ],
                [
                    'days' => '1',
                    'description' => 'H-1',
                    'users' => [5, 1] // User ID 5 dan 1 (Prepress dan Marketing)
                ]
            ]
        ]);

        // Setting untuk PIC Prepress Reminder
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'pic_prepress_reminder',
            'process_name' => 'PIC Prepress Reminder',
            'description' => 'Notifikasi reminder untuk PIC Prepress (H-3, H-2, H-1)',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => '3',
                    'description' => 'H-3 Reminder',
                    'users' => [] // Will be filled with actual PIC Prepress users
                ],
                [
                    'days' => '2',
                    'description' => 'H-2 Reminder',
                    'users' => [] // Will be filled with actual PIC Prepress users
                ],
                [
                    'days' => '1',
                    'description' => 'H-1 Reminder',
                    'users' => [] // Will be filled with actual PIC Prepress users
                ]
            ]
        ]);

        // Setting untuk Finish Prepress Process
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'finish_prepress',
            'process_name' => 'Job Prepress Selesai',
            'description' => 'Notifikasi ketika job prepress selesai dikerjakan',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => 'first',
                    'description' => 'Job Prepress Selesai',
                    'users' => [1, 5] // Marketing dan Prepress SPV
                ]
            ]
        ]);

        // Setting untuk Proses Produksi
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'proses_produksi',
            'process_name' => 'Proses Produksi',
            'description' => 'Notifikasi untuk proses produksi berdasarkan lead time configuration',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => 'first',
                    'description' => 'Lead Time Configuration Disimpan',
                    'users' => [1, 2] // User ID 1 (Marketing) dan 2 (PPIC)
                ],
                [
                    'days' => '3',
                    'description' => 'H-3 Produksi',
                    'users' => [1, 2] // User ID 1 (Marketing) dan 2 (PPIC)
                ],
                [
                    'days' => '1',
                    'description' => 'H-1 Produksi',
                    'users' => [1, 2, 3] // User ID 1 (Marketing), 2 (PPIC), dan 3 (Production)
                ]
            ]
        ]);

        // Setting untuk Job Deadline Fulltime
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'job_deadline_fulltime',
            'process_name' => 'Job Deadline Fulltime',
            'description' => 'Notifikasi reminder deadline job berdasarkan tanggal job_deadline',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => '10',
                    'description' => 'H-10 Deadline',
                    'users' => [1, 2] // User ID 1 (Marketing) dan 2 (PPIC)
                ],
                [
                    'days' => '5',
                    'description' => 'H-5 Deadline',
                    'users' => [1, 2, 3] // User ID 1 (Marketing), 2 (PPIC), dan 3 (Production)
                ],
                [
                    'days' => '0',
                    'description' => 'Hari H Deadline',
                    'users' => [1, 2, 3, 4, 5] // Semua stakeholder
                ]
            ]
        ]);

        // Setting untuk Progress Job
        DevelopmentEmailNotificationSetting::create([
            'process_code' => 'progress_job',
            'process_name' => 'Progress Job',
            'description' => 'Notifikasi setiap perpindahan status/progress job development',
            'is_active' => true,
            'reminder_schedule' => [
                [
                    'days' => 'first',
                    'description' => 'Status Job Berubah',
                    'users' => [1, 2] // User ID 1 (Marketing) dan 2 (PPIC)
                ]
            ]
        ]);
    }
}
