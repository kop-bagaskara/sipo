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
        Schema::connection('pgsql3')->create('tb_training_material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name'); // Nama kategori
            $table->text('description')->nullable(); // Deskripsi kategori
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
        Schema::connection('pgsql3')->dropIfExists('tb_training_material_categories');
    }
};

