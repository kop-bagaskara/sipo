<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailNotificationSetting;
use App\Models\User;

class EmailNotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat setting untuk Job Order Prepress
        $jobOrderSetting = EmailNotificationSetting::create([
            'notification_name' => 'Input Job Order',
            'notification_type' => 'job_order_prepress',
            'description' => 'Notifikasi email saat ada job order prepress baru',
            'is_active' => true
        ]);

        // Dapatkan user PPIC dan PREPRESS dengan jabatan HEAD/SPV
        $usersToNotify = User::where(function($query) {
            $query->where('divisi', 'PPIC')
                  ->orWhere('divisi', 'PREPRESS');
        })->where(function($query) {
            $query->where('jabatan', 'HEAD')
                  ->orWhere('jabatan', 'SPV');
        })->get();

        // Attach users ke setting
        if ($usersToNotify->isNotEmpty()) {
            $userData = [];
            foreach ($usersToNotify as $user) {
                $userData[$user->id] = ['is_active' => true];
            }
            $jobOrderSetting->users()->attach($userData);
        }

        // Buat setting untuk Job Order Production
        $productionSetting = EmailNotificationSetting::create([
            'notification_name' => 'Input Job Order Production',
            'notification_type' => 'job_order_production',
            'description' => 'Notifikasi email saat ada job order production baru',
            'is_active' => true
        ]);

        // Dapatkan user PPIC dengan jabatan HEAD/SPV
        $productionUsers = User::where('divisi', 'PPIC')
            ->where(function($query) {
                $query->where('jabatan', 'HEAD')
                      ->orWhere('jabatan', 'SPV');
            })->get();

        if ($productionUsers->isNotEmpty()) {
            $userData = [];
            foreach ($productionUsers as $user) {
                $userData[$user->id] = ['is_active' => true];
            }
            $productionSetting->users()->attach($userData);
        }
    }
}
