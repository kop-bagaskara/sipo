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
        Schema::connection('pgsql2')->create('tb_spl_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spl_request_id');
            $table->unsignedBigInteger('employee_id')->nullable(); // Link ke users (jika ada)
            $table->string('nip')->nullable(); // NIP dari masteremployee
            $table->string('employee_name'); // Nama karyawan
            $table->boolean('is_manual')->default(false); // Apakah input manual (true) atau dari masteremployee (false)
            $table->boolean('is_signed')->default(false); // Apakah sudah ditandatangani
            $table->timestamp('signed_at')->nullable(); // Waktu tanda tangan
            $table->timestamps();

            $table->foreign('spl_request_id')->references('id')->on('tb_spl_requests')->onDelete('cascade');
            $table->index(['spl_request_id', 'employee_id']);
            $table->index('is_signed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_spl_employees');
    }
};

