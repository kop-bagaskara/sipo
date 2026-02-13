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
        Schema::connection('pgsql3')->create('tb_training_session_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id'); // Relasi ke training_assignment
            $table->unsignedBigInteger('session_id'); // Relasi ke training_session
            $table->unsignedBigInteger('employee_id'); // User yang mengerjakan
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, passed, failed
            $table->decimal('score', 5, 2)->nullable(); // Skor yang didapat user
            $table->integer('correct_answers_count')->default(0); // Berapa jawaban benar
            $table->integer('total_questions')->default(0); // Total soal
            $table->json('questions_data')->nullable(); // Data soal-soal yang di-generate (biar konsisten per user)
            $table->json('answers_data')->nullable(); // Data jawaban user
            $table->timestamp('started_at')->nullable(); // Waktu mulai mengerjakan
            $table->timestamp('completed_at')->nullable(); // Waktu selesai mengerjakan
            $table->integer('duration_seconds')->nullable(); // Durasi pengerjaan (detik)
            $table->integer('attempts_count')->default(0); // Berapa kali mencoba
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('tb_training_assignments')
                  ->onDelete('cascade');

            $table->foreign('session_id')
                  ->references('id')
                  ->on('tb_training_sessions')
                  ->onDelete('cascade');

            // $table->foreign('employee_id')
            //       ->references('id')
            //       ->on('users')
            //       ->onDelete('cascade');

            // Indexes
            $table->index('assignment_id');
            $table->index('session_id');
            $table->index('employee_id');
            $table->index('status');

            // Unique: satu assignment hanya boleh punya satu progress per session
            $table->unique(['assignment_id', 'session_id'], 'unique_assignment_session_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_session_progress');
    }
};
