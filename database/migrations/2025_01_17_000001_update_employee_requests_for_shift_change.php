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
            // Tambahkan kolom untuk shift change yang lebih spesifik
            $table->string('applicant_name')->nullable()->after('employee_id');
            $table->string('applicant_department')->nullable()->after('applicant_name');
            $table->time('applicant_start_time')->nullable()->after('applicant_department');
            $table->time('applicant_end_time')->nullable()->after('applicant_start_time');
            $table->string('applicant_purpose')->nullable()->after('applicant_end_time');
            
            $table->string('substitute_name')->nullable()->after('applicant_purpose');
            $table->string('substitute_department')->nullable()->after('substitute_name');
            $table->time('substitute_start_time')->nullable()->after('substitute_department');
            $table->time('substitute_end_time')->nullable()->after('substitute_start_time');
            $table->string('substitute_purpose')->nullable()->after('substitute_end_time');
            
            $table->date('shift_date')->nullable()->after('substitute_purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_employee_requests', function (Blueprint $table) {
            $table->dropColumn([
                'applicant_name',
                'applicant_department', 
                'applicant_start_time',
                'applicant_end_time',
                'applicant_purpose',
                'substitute_name',
                'substitute_department',
                'substitute_start_time', 
                'substitute_end_time',
                'substitute_purpose',
                'shift_date'
            ]);
        });
    }
};
