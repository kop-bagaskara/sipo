<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed data shift kerja
        DB::connection('pgsql2')->table('tb_work_shifts')->insert([
            [
                'shift_name' => 'Shift Pagi',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'description' => 'Shift kerja pagi hari',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'shift_name' => 'Shift Siang',
                'start_time' => '16:00:00',
                'end_time' => '00:00:00',
                'description' => 'Shift kerja siang hari',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'shift_name' => 'Shift Malam',
                'start_time' => '00:00:00',
                'end_time' => '08:00:00',
                'description' => 'Shift kerja malam hari',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed data jenis ketidakhadiran - sesuai form "PERMOHONAN TIDAK MASUK KERJA"
        DB::connection('pgsql2')->table('tb_absence_types')->insert([
            [
                'type_name' => 'Dinas',
                'description' => 'Ketidakhadiran untuk keperluan dinas',
                'requires_medical_certificate' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Cuti Tahunan',
                'description' => 'Menggunakan hak cuti tahunan',
                'requires_medical_certificate' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Cuti Khusus',
                'description' => 'Cuti untuk keperluan khusus',
                'requires_medical_certificate' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Cuti Haid',
                'description' => 'Cuti haid untuk karyawan wanita',
                'requires_medical_certificate' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Cuti Hamil',
                'description' => 'Cuti hamil untuk karyawan wanita',
                'requires_medical_certificate' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Ijin',
                'description' => 'Ijin dengan alasan tertentu',
                'requires_medical_certificate' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed data email settings HR
        DB::connection('pgsql2')->table('tb_hr_email_settings')->insert([
            [
                'setting_name' => 'Notifikasi Pengajuan Baru',
                'setting_key' => 'new_request_notification',
                'description' => 'Email notifikasi ketika ada pengajuan baru',
                'recipient_roles' => json_encode(['supervisor', 'hr']),
                'email_template' => json_encode([
                    'subject' => 'Pengajuan Baru - {request_type}',
                    'body' => 'Ada pengajuan baru dari {employee_name} yang memerlukan approval.'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_name' => 'Reminder Approval Atasan',
                'setting_key' => 'supervisor_reminder',
                'description' => 'Email reminder untuk approval atasan',
                'recipient_roles' => json_encode(['supervisor']),
                'email_template' => json_encode([
                    'subject' => 'Reminder: Approval Pengajuan - {request_number}',
                    'body' => 'Anda memiliki pengajuan yang menunggu approval sejak {days} hari.'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_name' => 'Notifikasi Status Pengajuan',
                'setting_key' => 'request_status_update',
                'description' => 'Email notifikasi perubahan status pengajuan',
                'recipient_roles' => json_encode(['employee']),
                'email_template' => json_encode([
                    'subject' => 'Update Status Pengajuan - {request_number}',
                    'body' => 'Pengajuan Anda telah {status} dengan catatan: {notes}'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql2')->table('tb_hr_email_settings')->truncate();
        DB::connection('pgsql2')->table('tb_absence_types')->truncate();
        DB::connection('pgsql2')->table('tb_work_shifts')->truncate();
    }
};
