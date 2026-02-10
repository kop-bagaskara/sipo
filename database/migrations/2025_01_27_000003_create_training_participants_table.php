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
        Schema::create('tb_training_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id'); // ID training
            $table->unsignedBigInteger('employee_id'); // ID karyawan
            $table->enum('registration_status', [
                'registered',       // Terdaftar
                'approved',         // Disetujui
                'rejected',         // Ditolak
                'attended',         // Hadir
                'completed',        // Selesai
                'cancelled'         // Dibatalkan
            ])->default('registered');
            $table->enum('registration_type', [
                'mandatory',        // Wajib (otomatis)
                'voluntary',        // Sukarela
                'recommended'       // Direkomendasikan
            ])->default('voluntary');
            $table->timestamp('registered_at')->nullable(); // Waktu pendaftaran
            $table->timestamp('approved_at')->nullable(); // Waktu disetujui
            $table->timestamp('rejected_at')->nullable(); // Waktu ditolak
            $table->timestamp('attended_at')->nullable(); // Waktu hadir
            $table->timestamp('completed_at')->nullable(); // Waktu selesai
            $table->timestamp('cancelled_at')->nullable(); // Waktu dibatalkan
            $table->text('rejection_reason')->nullable(); // Alasan penolakan
            $table->text('cancellation_reason')->nullable(); // Alasan pembatalan
            $table->decimal('score', 5, 2)->nullable(); // Nilai/score
            $table->text('feedback')->nullable(); // Feedback peserta
            $table->text('instructor_notes')->nullable(); // Catatan instruktur
            $table->json('attendance_data')->nullable(); // Data kehadiran (JSON)
            $table->json('assessment_data')->nullable(); // Data penilaian (JSON)
            $table->boolean('certificate_issued')->default(false); // Sertifikat diterbitkan
            $table->string('certificate_number')->nullable(); // Nomor sertifikat
            $table->timestamp('certificate_issued_at')->nullable(); // Waktu sertifikat diterbitkan
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('approved_by')->nullable(); // Disetujui oleh
            $table->unsignedBigInteger('rejected_by')->nullable(); // Ditolak oleh
            $table->timestamps();

            // Foreign keys
            $table->foreign('training_id')->references('id')->on('tb_training_masters')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint untuk mencegah duplikasi partisipasi
            $table->unique(['training_id', 'employee_id']);
            
            // Indexes untuk performa
            $table->index(['training_id', 'registration_status']);
            $table->index(['employee_id', 'registration_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_training_participants');
    }
};
