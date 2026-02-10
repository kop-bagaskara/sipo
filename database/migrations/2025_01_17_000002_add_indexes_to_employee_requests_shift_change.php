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
        Schema::connection('pgsql2')->table('tb_employee_requests', function (Blueprint $table) {
            // Add indexes for better performance on shift change queries
            $table->index(['request_type', 'shift_date'], 'idx_shift_change_date');
            $table->index(['applicant_name', 'shift_date'], 'idx_applicant_shift_date');
            $table->index(['substitute_name', 'shift_date'], 'idx_substitute_shift_date');
            $table->index(['status', 'shift_date'], 'idx_status_shift_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_employee_requests', function (Blueprint $table) {
            $table->dropIndex('idx_shift_change_date');
            $table->dropIndex('idx_applicant_shift_date');
            $table->dropIndex('idx_substitute_shift_date');
            $table->dropIndex('idx_status_shift_date');
        });
    }
};
