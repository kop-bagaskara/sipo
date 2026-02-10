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
        Schema::connection('pgsql3')->create('tb_training_exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id'); // Relasi ke assignment
            $table->unsignedBigInteger('material_id'); // Materi yang diujikan
            $table->unsignedBigInteger('employee_id'); // Karyawan yang ujian
            $table->json('question_ids')->nullable(); // Array ID soal yang diberikan (random per user) (JSON)
            $table->json('user_answers')->nullable(); // Jawaban user (JSON: {question_id: answer})
            $table->decimal('score', 5, 2)->default(0.00); // Skor yang didapat
            $table->decimal('max_score', 5, 2)->default(0.00); // Skor maksimal
            $table->decimal('passing_score', 5, 2)->default(70.00); // Skor minimum untuk lulus
            $table->enum('status', [
                'not_started', // Belum mulai
                'in_progress', // Sedang dikerjakan
                'completed',   // Selesai
                'expired'      // Expired/melewati waktu
            ])->default('not_started');
            $table->integer('total_questions')->default(0); // Total soal
            $table->integer('correct_answers')->default(0); // Jumlah jawaban benar
            $table->integer('wrong_answers')->default(0); // Jumlah jawaban salah
            $table->timestamp('started_at')->nullable(); // Waktu mulai ujian
            $table->timestamp('completed_at')->nullable(); // Waktu selesai ujian
            $table->integer('duration_seconds')->default(0); // Durasi pengerjaan dalam detik
            $table->integer('time_limit_seconds')->nullable(); // Batas waktu ujian (detik)
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();

            // Foreign keys
            $table->foreign('assignment_id')->references('id')->on('tb_training_assignments')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('tb_training_materials')->onDelete('cascade');
            // $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('assignment_id');
            $table->index('material_id');
            $table->index('employee_id');
            $table->index('status');

            // Unique constraint: satu employee hanya punya satu ujian per materi per assignment
            $table->unique(['assignment_id', 'material_id', 'employee_id'], 'unique_material_exam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_exams');
    }
};

