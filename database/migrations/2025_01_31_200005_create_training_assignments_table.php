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
        Schema::connection('pgsql3')->create('tb_training_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id')->nullable(); // Relasi ke training master (opsional)
            $table->unsignedBigInteger('employee_id'); // Karyawan yang di-assign
            $table->json('material_ids')->nullable(); // Array ID materi yang harus diselesaikan (JSON)
            $table->enum('status', [
                'assigned',    // Sudah di-assign
                'in_progress', // Sedang dikerjakan
                'completed',  // Selesai
                'expired'      // Expired/melewati deadline
            ])->default('assigned');
            $table->date('assigned_date'); // Tanggal assign
            $table->date('deadline_date')->nullable(); // Deadline
            $table->decimal('progress_percentage', 5, 2)->default(0.00); // Progress persentase
            $table->text('notes')->nullable(); // Catatan
            $table->unsignedBigInteger('assigned_by')->nullable(); // Admin yang assign
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('assigned_date');
            $table->index('deadline_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_assignments');
    }
};

