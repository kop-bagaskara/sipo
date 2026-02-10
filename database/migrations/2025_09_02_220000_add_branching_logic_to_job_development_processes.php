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
        Schema::table('tb_job_development_processes', function (Blueprint $table) {
            // Add branching logic fields
            $table->enum('process_type', ['normal', 'ppic', 'purchasing', 'qc', 'rnd_verification'])->default('normal')->after('process_name');
            $table->enum('branch_type', ['proof', 'trial_khusus'])->default('proof')->after('process_type');
            $table->json('branch_conditions')->nullable()->after('branch_type'); // Store conditions for branching
            $table->timestamp('scheduled_at')->nullable()->after('estimated_duration'); // For PPIC scheduling
            $table->text('verification_notes')->nullable()->after('notes'); // For QC and RnD verification
            $table->enum('verification_result', ['pending', 'ok', 'not_ok'])->default('pending')->after('verification_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_development_processes', function (Blueprint $table) {
            $table->dropColumn([
                'process_type', 
                'branch_type', 
                'branch_conditions', 
                'scheduled_at', 
                'verification_notes', 
                'verification_result'
            ]);
        });
    }
};
