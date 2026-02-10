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
        Schema::connection('pgsql2')->create('tb_overtime_entries', function (Blueprint $table) {
            $table->id();
            $table->date('request_date');                    // tanggal lembur
            $table->string('location');                     // lokasi
            $table->unsignedBigInteger('employee_id')->nullable(); // link ke users (optional)
            $table->string('employee_name');               // nama karyawan (denormalized)
            $table->string('department');                   // bagian
            $table->time('start_time');                     // jam mulai
            $table->time('end_time');                       // jam selesai
            $table->text('job_description');               // keterangan pekerjaan
            $table->unsignedInteger('divisi_id');          // untuk filter per divisi
            $table->string('status')->default('pending_spv'); // status approval
            
            // SPV approval
            $table->unsignedBigInteger('spv_id')->nullable();
            $table->text('spv_notes')->nullable();
            $table->timestamp('spv_at')->nullable();
            
            // Head approval
            $table->unsignedBigInteger('head_id')->nullable();
            $table->text('head_notes')->nullable();
            $table->timestamp('head_at')->nullable();
            
            // HRGA approval
            $table->unsignedBigInteger('hrga_id')->nullable();
            $table->text('hrga_notes')->nullable();
            $table->timestamp('hrga_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['divisi_id', 'request_date']);
            $table->index(['status']);
            $table->index(['employee_id']);
            $table->index(['spv_id']);
            $table->index(['head_id']);
            $table->index(['hrga_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_overtime_entries');
    }
};
