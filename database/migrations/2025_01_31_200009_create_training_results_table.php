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
        Schema::connection('pgsql3')->create('tb_training_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id'); // Relasi ke assignment
            $table->unsignedBigInteger('employee_id'); // Karyawan
            $table->decimal('total_score', 5, 2)->default(0.00); // Total skor dari semua ujian
            $table->decimal('max_possible_score', 5, 2)->default(0.00); // Skor maksimal yang mungkin
            $table->decimal('minimum_passing_score', 5, 2)->default(70.00); // Skor minimum untuk lulus
            $table->decimal('final_percentage', 5, 2)->default(0.00); // Persentase akhir
            $table->enum('status', [
                'in_progress', // Masih dalam proses
                'passed',      // Lulus
                'failed',      // Tidak lulus
                'expired'      // Expired
            ])->default('in_progress');
            $table->date('completed_date')->nullable(); // Tanggal selesai
            $table->string('certificate_path')->nullable(); // Path sertifikat (jika lulus)
            $table->string('certificate_number')->nullable()->unique(); // Nomor sertifikat
            $table->text('notes')->nullable(); // Catatan
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assignment_id')->references('id')->on('tb_training_assignments')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('assignment_id');
            $table->index('employee_id');
            $table->index('status');
            $table->index('completed_date');

            // Unique constraint: satu employee hanya punya satu result per assignment
            $table->unique(['assignment_id', 'employee_id'], 'unique_assignment_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_results');
    }
};

