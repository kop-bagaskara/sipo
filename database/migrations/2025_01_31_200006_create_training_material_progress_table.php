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
        Schema::connection('pgsql3')->create('tb_training_material_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id'); // Relasi ke assignment
            $table->unsignedBigInteger('material_id'); // Materi yang sedang dikerjakan
            $table->unsignedBigInteger('employee_id'); // Karyawan
            $table->enum('status', [
                'not_started', // Belum mulai
                'watching',   // Sedang menonton
                'completed'   // Selesai menonton
            ])->default('not_started');
            $table->decimal('progress_percentage', 5, 2)->default(0.00); // Progress persentase (0-100)
            $table->timestamp('started_at')->nullable(); // Waktu mulai menonton
            $table->timestamp('completed_at')->nullable(); // Waktu selesai menonton
            $table->integer('watch_duration_seconds')->default(0); // Durasi menonton dalam detik
            $table->integer('last_position_seconds')->default(0); // Posisi terakhir video (detik)
            $table->timestamps();

            // Foreign keys
            $table->foreign('assignment_id')->references('id')->on('tb_training_assignments')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('tb_training_materials')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('assignment_id');
            $table->index('material_id');
            $table->index('employee_id');
            $table->index('status');

            // Unique constraint: satu employee hanya punya satu progress per materi per assignment
            $table->unique(['assignment_id', 'material_id', 'employee_id'], 'unique_material_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_material_progress');
    }
};

