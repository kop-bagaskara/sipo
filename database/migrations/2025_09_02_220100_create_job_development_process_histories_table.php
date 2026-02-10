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
        Schema::create('tb_job_development_process_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_development_id')->constrained('tb_job_developments')->onDelete('cascade');
            $table->foreignId('process_id')->constrained('tb_job_development_processes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // User who performed the action
            $table->enum('action_type', ['started', 'completed', 'verified', 'scheduled', 'purchased', 'received', 'qc_checked', 'rnd_verified']);
            $table->enum('action_result', ['success', 'failed', 'pending', 'ok', 'not_ok'])->default('pending');
            $table->text('action_notes')->nullable();
            $table->json('action_data')->nullable(); // Store additional data like purchase details, QC results, etc.
            $table->timestamp('action_at');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_development_id', 'process_id']);
            $table->index(['action_type', 'action_result']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_job_development_process_histories');
    }
};
