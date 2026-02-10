<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('pgsql2')->create('tb_employee_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // Nomor pengajuan otomatis
            $table->enum('request_type', [
                'shift_change',      // Permohonan Tukar Shift
                'absence',           // Permohonan Tidak Masuk Kerja
                'overtime',          // Surat Perintah Lembur
                'vehicle_asset'      // Permintaan Membawa Kendaraan/Inventaris
            ]);
            $table->unsignedBigInteger('employee_id'); // ID karyawan yang mengajukan
            $table->unsignedBigInteger('supervisor_id')->nullable(); // ID atasan langsung
            $table->unsignedBigInteger('hr_id')->nullable(); // ID HR yang menangani

            // Status approval - sesuai dengan form
            $table->enum('status', [
                'pending',           // Menunggu approval atasan
                'supervisor_approved', // Disetujui atasan, menunggu HR
                'supervisor_rejected', // Ditolak atasan
                'hr_approved',       // Disetujui HR (final)
                'hr_rejected',       // Ditolak HR (final)
                'cancelled'          // Dibatalkan oleh pengaju
            ])->default('pending');

            // Data pengajuan (JSON untuk fleksibilitas)
            $table->json('request_data'); // Data spesifik sesuai jenis pengajuan

            // Approval atasan
            $table->text('supervisor_notes')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();
            $table->timestamp('supervisor_rejected_at')->nullable();

            // Approval HR
            $table->text('hr_notes')->nullable();
            $table->timestamp('hr_approved_at')->nullable();
            $table->timestamp('hr_rejected_at')->nullable();

            // Metadata
            $table->text('notes')->nullable(); // Catatan tambahan dari pengaju
            $table->string('attachment_path')->nullable(); // File lampiran
            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index(['supervisor_id', 'status']);
            $table->index(['hr_id', 'status']);
            $table->index(['request_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_employee_requests');
    }
};
