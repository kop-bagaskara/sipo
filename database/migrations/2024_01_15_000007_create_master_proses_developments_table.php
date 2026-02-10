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
        Schema::create('tb_master_proses_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_development_id');
            $table->string('proses_name'); // Nama proses (Marketing Input, RnD Send to Prepress, dll)
            $table->integer('urutan_proses'); // Urutan 1-12
            $table->string('department_responsible'); // Department yang bertanggung jawab
            $table->enum('status_proses', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamp('started_at')->nullable(); // Kapan proses dimulai
            $table->timestamp('completed_at')->nullable(); // Kapan proses selesai
            $table->unsignedBigInteger('completed_by')->nullable(); // Siapa yang menyelesaikan
            $table->integer('expected_days')->nullable(); // Berapa hari proses diharapkan selesai
            $table->boolean('is_required')->default(true); // Apakah proses ini wajib atau opsional
            $table->timestamps();

            // Foreign key
            $table->foreign('job_order_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');

            // Index
            $table->index(['job_order_development_id', 'urutan_proses']);
            $table->index(['department_responsible', 'status_proses']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_master_proses_developments');
    }
};
