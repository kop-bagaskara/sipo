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
        Schema::create('tb_training_masters', function (Blueprint $table) {
            $table->id();
            $table->string('training_code')->unique(); // Kode training unik
            $table->string('training_name'); // Nama training
            $table->text('description')->nullable(); // Deskripsi training
            $table->text('objectives')->nullable(); // Tujuan training
            $table->text('prerequisites')->nullable(); // Prasyarat training
            $table->enum('training_type', [
                'mandatory',    // Training wajib
                'optional',     // Training opsional
                'certification', // Training sertifikasi
                'skill_development' // Pengembangan skill
            ]);
            $table->enum('training_method', [
                'classroom',    // Kelas tatap muka
                'online',       // Online
                'hybrid',       // Hybrid
                'workshop',     // Workshop
                'seminar'       // Seminar
            ]);
            $table->integer('duration_hours'); // Durasi dalam jam
            $table->integer('max_participants')->nullable(); // Maksimal peserta
            $table->integer('min_participants')->default(1); // Minimal peserta
            $table->decimal('cost_per_participant', 10, 2)->nullable(); // Biaya per peserta
            $table->string('instructor_name')->nullable(); // Nama instruktur
            $table->string('instructor_contact')->nullable(); // Kontak instruktur
            $table->enum('status', [
                'draft',        // Draft
                'published',    // Diterbitkan
                'ongoing',      // Sedang berlangsung
                'completed',    // Selesai
                'cancelled'     // Dibatalkan
            ])->default('draft');
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('target_departments')->nullable(); // Departemen target (JSON array)
            $table->json('target_positions')->nullable(); // Posisi target (JSON array)
            $table->json('target_levels')->nullable(); // Level target (JSON array)
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('created_by'); // Dibuat oleh
            $table->unsignedBigInteger('updated_by')->nullable(); // Diupdate oleh
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_training_masters');
    }
};
