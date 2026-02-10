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
        // Tabel untuk detail karyawan lembur - sesuai form "SURAT PERINTAH LEMBUR"
        Schema::connection('pgsql2')->create('tb_overtime_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_name');
            $table->string('department');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('job_description');
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('tb_employee_requests')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['request_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_overtime_employees');
    }
};
