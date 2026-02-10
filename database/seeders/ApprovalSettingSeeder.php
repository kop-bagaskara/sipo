<?php

namespace Database\Seeders;

use App\Models\ApprovalSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some sample users for approval settings
        $users = User::take(5)->get();

        if ($users->count() < 2) {
            $this->command->warn('Not enough users found. Please create at least 2 users first.');
            return;
        }

        // Clear existing approval settings
        ApprovalSetting::truncate();

        // Sample approval settings for shift change
        $shiftChangeApprovals = [
            [
                'request_type' => 'shift_change',
                'approval_level' => 'supervisor',
                'approval_order' => 1,
                'user_id' => $users[0]->id,
                'user_name' => $users[0]->name,
                'user_position' => 'Supervisor Produksi',
                'is_active' => true,
                'description' => 'Atasan langsung yang menyetujui pengajuan tukar shift'
            ],
            [
                'request_type' => 'shift_change',
                'approval_level' => 'hr',
                'approval_order' => 2,
                'user_id' => $users[1]->id,
                'user_name' => $users[1]->name,
                'user_position' => 'HR Manager',
                'is_active' => true,
                'description' => 'HR yang menyetujui pengajuan tukar shift'
            ]
        ];

        // Sample approval settings for absence
        $absenceApprovals = [
            [
                'request_type' => 'absence',
                'approval_level' => 'supervisor',
                'approval_order' => 1,
                'user_id' => $users[0]->id,
                'user_name' => $users[0]->name,
                'user_position' => 'Supervisor Produksi',
                'is_active' => true,
                'description' => 'Atasan langsung yang menyetujui pengajuan tidak masuk kerja'
            ],
            [
                'request_type' => 'absence',
                'approval_level' => 'hr',
                'approval_order' => 2,
                'user_id' => $users[1]->id,
                'user_name' => $users[1]->name,
                'user_position' => 'HR Manager',
                'is_active' => true,
                'description' => 'HR yang menyetujui pengajuan tidak masuk kerja'
            ]
        ];

        // Sample approval settings for overtime
        $overtimeApprovals = [
            [
                'request_type' => 'overtime',
                'approval_level' => 'supervisor',
                'approval_order' => 1,
                'user_id' => $users[0]->id,
                'user_name' => $users[0]->name,
                'user_position' => 'Supervisor Produksi',
                'is_active' => true,
                'description' => 'Atasan langsung yang menyetujui pengajuan lembur'
            ],
            [
                'request_type' => 'overtime',
                'approval_level' => 'hr',
                'approval_order' => 2,
                'user_id' => $users[1]->id,
                'user_name' => $users[1]->name,
                'user_position' => 'HR Manager',
                'is_active' => true,
                'description' => 'HR yang menyetujui pengajuan lembur'
            ]
        ];

        // Sample approval settings for vehicle asset
        $vehicleAssetApprovals = [
            [
                'request_type' => 'vehicle_asset',
                'approval_level' => 'supervisor',
                'approval_order' => 1,
                'user_id' => $users[0]->id,
                'user_name' => $users[0]->name,
                'user_position' => 'Supervisor Produksi',
                'is_active' => true,
                'description' => 'Atasan langsung yang menyetujui pengajuan kendaraan'
            ],
            [
                'request_type' => 'vehicle_asset',
                'approval_level' => 'hr',
                'approval_order' => 2,
                'user_id' => $users[1]->id,
                'user_name' => $users[1]->name,
                'user_position' => 'HR Manager',
                'is_active' => true,
                'description' => 'HR yang menyetujui pengajuan kendaraan'
            ]
        ];

        // Insert all approval settings
        $allApprovals = array_merge(
            $shiftChangeApprovals,
            $absenceApprovals,
            $overtimeApprovals,
            $vehicleAssetApprovals
        );

        foreach ($allApprovals as $approval) {
            ApprovalSetting::create($approval);
        }

        $this->command->info('Approval settings seeded successfully!');
    }
}
