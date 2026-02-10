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
        Schema::connection('pgsql2')->create('tb_ishihara_plates', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique(); // Nomor pelat (misal: PLATE-001)
            $table->string('image_path'); // Path ke gambar (misal: images/ishihara/plate-1.jpg)
            $table->string('correct_answer'); // Jawaban benar (misal: "12", "8", "6")
            $table->integer('difficulty_level')->default(1); // Level kesulitan (1-5)
            $table->text('description')->nullable(); // Deskripsi tambahan
            $table->boolean('is_active')->default(true); // Aktif/tidak aktif
            $table->integer('display_order')->default(0); // Urutan tampil (untuk admin)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_ishihara_plates');
    }
};

