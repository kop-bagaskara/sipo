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
        Schema::create('tb_ppic_paper_meeting_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->string('product_name')->comment('Nama produk (contoh: Carton Juara Berry)');
            $table->string('product_category')->nullable()->comment('Kategori produk (contoh: Carton, Pack Packaging, Inner Frame)');

            // Quantity per bulan
            $table->bigInteger('quantity_month_1')->default(0)->comment('Quantity bulan pertama');
            $table->bigInteger('quantity_month_2')->default(0)->comment('Quantity bulan kedua');
            $table->bigInteger('quantity_month_3')->default(0)->comment('Quantity bulan ketiga');
            $table->bigInteger('total_quantity')->default(0)->comment('Total quantity 3 bulan');
            $table->bigInteger('total_with_tolerance')->default(0)->comment('Total dengan toleransi');

            // Urutan tampil
            $table->integer('sort_order')->default(0)->comment('Urutan tampil di view');

            $table->timestamps();

            // Foreign key
            $table->foreign('meeting_id')->references('id')->on('tb_ppic_paper_meetings')->onDelete('cascade');

            // Indexes
            $table->index('meeting_id');
            $table->index('product_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_items');
    }
};

