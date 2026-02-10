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
        Schema::connection('pgsql3')->create('tb_training_difficulty_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_code')->unique(); // Kode level (e.g., 'EASY', 'MEDIUM')
            $table->string('level_name'); // Nama level (Paling Mudah, Mudah, Cukup, Menengah Ke Atas, Sulit)
            $table->text('description')->nullable(); // Deskripsi level
            $table->decimal('score_multiplier', 5, 2)->default(1.00); // Multiplier untuk skor (opsional)
            $table->integer('display_order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true); // Status aktif
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_difficulty_levels');
    }
};

