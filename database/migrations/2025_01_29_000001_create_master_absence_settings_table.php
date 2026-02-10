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
        Schema::create('master_absence_settings', function (Blueprint $table) {
            $table->id();
            $table->string('absence_type', 50)->unique()->comment('Jenis absence: Dinas, Cuti Tahunan, Ijin, Sakit, dll');
            $table->integer('min_deadline_days')->default(0)->comment('Batas minimum pengajuan dalam hari (0 = hari ini, -1 = kemarin, 7 = 7 hari ke depan)');
            $table->integer('max_deadline_days')->nullable()->comment('Batas maksimum pengajuan dalam hari (null = unlimited, 1 = besok, -1 = kemarin)');
            $table->boolean('attachment_required')->default(false)->comment('Apakah wajib lampiran');
            $table->string('deadline_text', 255)->nullable()->comment('Teks informasi deadline untuk user');
            $table->text('description')->nullable()->comment('Deskripsi tambahan');
            $table->boolean('is_active')->default(true)->comment('Apakah setting aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_absence_settings');
    }
};
