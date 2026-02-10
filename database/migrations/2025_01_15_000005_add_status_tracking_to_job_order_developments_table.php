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
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            // Tambahkan field untuk tracking status yang lebih detail
            $table->enum('status_job', ['OPEN', 'IN_PROGRESS', 'COMPLETED', 'ASSIGNED_TO_PPIC', 'CANCELLED'])->default('OPEN')->change();
            
            // Tambahkan field untuk tracking progress
            $table->text('progress_notes')->nullable()->after('catatan');
            $table->timestamp('started_at')->nullable()->after('assigned_to_ppic_by');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            $table->string('status_job')->default('OPEN')->change();
            $table->dropColumn(['progress_notes', 'started_at', 'completed_at']);
        });
    }
};
