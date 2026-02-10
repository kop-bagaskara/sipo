<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAbsenceSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'absence_type' => 'Dinas',
                'min_deadline_days' => 1, // H-1
                'max_deadline_days' => null, // Unlimited ke depan
                'attachment_required' => true,
                'deadline_text' => 'Pengajuan harus H-1 (1 hari sebelum tanggal izin)',
                'description' => 'Dinas Luar - Perjalanan dinas kerja',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Cuti Tahunan',
                'min_deadline_days' => 7, // H-7
                'max_deadline_days' => null, // Unlimited ke depan
                'attachment_required' => false,
                'deadline_text' => 'Pengajuan harus H-7 (7 hari sebelum tanggal izin)',
                'description' => 'Cuti Tahunan - Mengurangi jatah cuti tahunan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Cuti Khusus',
                'min_deadline_days' => 0, // Hari ini
                'max_deadline_days' => null, // Unlimited
                'attachment_required' => true,
                'deadline_text' => 'Pengajuan bisa dilakukan kapan saja',
                'description' => 'Cuti Khusus - Tidak mengurangi jatah cuti tahunan (Pernikahan, Kematian, dll)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Cuti Haid',
                'min_deadline_days' => null, // Unlimited ke belakang
                'max_deadline_days' => 1, // H+1
                'attachment_required' => true,
                'deadline_text' => 'Pengajuan maksimal H+1 (1 hari setelah tanggal izin)',
                'description' => 'Cuti Haid - Waktu cuti 3 bulan total',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Cuti Hamil',
                'min_deadline_days' => null, // N/A (dihitung dari HPL)
                'max_deadline_days' => null, // N/A
                'attachment_required' => true,
                'deadline_text' => 'Waktu cuti 3 bulan total (1.5 bulan sebelum HPL sampai 1.5 bulan setelah HPL)',
                'description' => 'Cuti Hamil - Tanggal dihitung otomatis dari HPL',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Ijin',
                'min_deadline_days' => -1, // H-1
                'max_deadline_days' => 1, // H+1
                'attachment_required' => false,
                'deadline_text' => 'Pengajuan maksimal H+1 atau H-1 (1 hari setelah atau 1 hari sebelum tanggal izin)',
                'description' => 'Ijin - Izin singkat tidak masuk kerja',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'absence_type' => 'Sakit',
                'min_deadline_days' => 0, // Hari ini
                'max_deadline_days' => 1, // H+1
                'attachment_required' => true,
                'deadline_text' => 'Pengajuan dapat dilakukan saat sakit atau H+1 ketika masuk kerja',
                'description' => 'Sakit - Wajib melampirkan surat dokter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('master_absence_settings')->insert($settings);
    }
}
