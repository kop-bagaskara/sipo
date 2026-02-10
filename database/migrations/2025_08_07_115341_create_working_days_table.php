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
        Schema::create('tb_working_days', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->unique(); // 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu, 7=Minggu
            $table->string('day_name', 20); // Nama hari dalam bahasa Indonesia
            $table->boolean('is_working_day')->default(true); // Apakah hari kerja atau tidak
            $table->decimal('working_hours', 4, 2)->default(8.00); // Jam kerja normal (bisa desimal, misal 7.5 jam)
            $table->boolean('is_half_day')->default(false); // Flag untuk setengah hari
            $table->decimal('half_day_hours', 4, 2)->default(4.00); // Jam kerja setengah hari
            $table->boolean('is_active')->default(true); // Untuk enable/disable konfigurasi
            $table->text('description')->nullable(); // Keterangan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_working_days');
    }
};
