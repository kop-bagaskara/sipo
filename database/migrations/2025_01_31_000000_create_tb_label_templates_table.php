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
        Schema::create('tb_label_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable(); // Relasi ke customer
            $table->string('template_name'); // Nama template, contoh: "Template Label Besar CARTON COKLAT"
            $table->enum('template_type', ['besar', 'kecil']); // Besar atau Kecil
            $table->string('brand_name')->nullable(); // Nama brand, contoh: "NABATI", "VIDORAN", "FUKUMI", dll
            $table->string('product_name')->nullable(); // Nama produk, contoh: "COKLAT", "PISANG", dll
            $table->string('file_path'); // Path ke file template di server
            $table->string('file_name'); // Nama file asli
            $table->integer('file_size')->nullable(); // Ukuran file dalam bytes
            $table->text('description')->nullable(); // Deskripsi template
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('customer_id')->references('id')->on('tb_label_customers')->onDelete('set null');

            // Indexes
            $table->index('customer_id');
            $table->index('template_type');
            $table->index('brand_name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_label_templates');
    }
};

