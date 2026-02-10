<?php

namespace Database\Seeders;

use App\Models\ApprovalSetting;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk membuat master approval setting SPL di tb_approval_hr_setting
 *
 * Contoh approval flow untuk SPL:
 * 1. SPV Division (jabatan 5) - Order 1
 * 2. HEAD Division (jabatan 3, 4, atau 5) - Order 2
 * 3. Manager (jabatan 3) - Order 3
 * 4. HRD (role hr) - Order 4 (final)
 *
 * Catatan: Sesuaikan urutan dan role sesuai kebutuhan bisnis
 */
class SplApprovalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus approval setting SPL yang sudah ada (jika ada)
        ApprovalSetting::where('request_type', 'spl')->delete();

        // Contoh approval flow untuk SPL
        // Sesuaikan dengan kebutuhan bisnis Anda

        // Order 1: SPV Division
        ApprovalSetting::create([
            'request_type' => 'spl',
            'approval_level' => 'spv_division',
            'approval_order' => 1,
            'approver_type' => 'role',
            'role_key' => 'spv_division',
            'allowed_jabatan' => [5], // SPV
            'user_id' => null,
            'user_name' => 'SPV per Divisi',
            'user_position' => 'SPV',
            'is_active' => true,
            'description' => 'SPV per Divisi yang menyetujui SPL'
        ]);

        // Order 2: HEAD Division
        ApprovalSetting::create([
            'request_type' => 'spl',
            'approval_level' => 'head_division',
            'approval_order' => 2,
            'approver_type' => 'role',
            'role_key' => 'head_division',
            'allowed_jabatan' => [3, 4, 5], // MANAGER, HEAD, SPV
            'user_id' => null,
            'user_name' => 'HEAD per Divisi',
            'user_position' => 'HEAD/MANAGER/SPV',
            'is_active' => true,
            'description' => 'HEAD per Divisi yang menyetujui SPL'
        ]);

        // Order 3: Manager (opsional, bisa di-disable jika tidak diperlukan)
        ApprovalSetting::create([
            'request_type' => 'spl',
            'approval_level' => 'manager',
            'approval_order' => 3,
            'approver_type' => 'role',
            'role_key' => 'manager',
            'allowed_jabatan' => [3], // MANAGER
            'user_id' => null,
            'user_name' => 'Manager',
            'user_position' => 'MANAGER',
            'is_active' => true,
            'description' => 'Manager yang menyetujui SPL'
        ]);

        // Order 4: HRD (final approval)
        ApprovalSetting::create([
            'request_type' => 'spl',
            'approval_level' => 'hr',
            'approval_order' => 4,
            'approver_type' => 'role',
            'role_key' => 'hr',
            'allowed_jabatan' => null, // Semua HRD
            'user_id' => null,
            'user_name' => 'Semua HRD',
            'user_position' => 'HRD',
            'is_active' => true,
            'description' => 'HRD yang menyetujui SPL (final approval)'
        ]);

        $this->command->info('SPL Approval Settings berhasil dibuat!');
        $this->command->info('Silakan sesuaikan approval_order dan is_active sesuai kebutuhan.');
    }
}

