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
        Schema::create('tb_label_generations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id'); // Relasi ke template
            $table->unsignedBigInteger('customer_id'); // Relasi ke customer
            $table->json('field_values'); // Nilai field yang diisi saat generate
            $table->string('pdf_file_path'); // Path file PDF yang di-generate
            $table->string('pdf_file_name'); // Nama file PDF
            $table->integer('quantity')->default(1); // Jumlah label yang di-generate
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('template_id')->references('id')->on('tb_label_templates')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('tb_label_customers')->onDelete('cascade');

            // Indexes
            $table->index('template_id');
            $table->index('customer_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_label_generations');
    }
};

