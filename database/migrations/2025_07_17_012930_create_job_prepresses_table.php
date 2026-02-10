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
        Schema::create('tb_job_prepresses', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_job_order')->unique();
            $table->datetime('tanggal_job_order');
            $table->datetime('tanggal_deadline');
            $table->string('customer');
            $table->string('product');
            $table->string('kode_design');
            $table->string('dimension')->nullable();
            $table->string('material')->nullable();
            $table->integer('total_colour');
            $table->string('total_colour_details')->nullable();
            $table->integer('qty_order_estimation');
            $table->string('job_order')->nullable();
            $table->string('file_data')->nullable();
            $table->string('created_by')->nullable();
            $table->string('changed_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_job_prepresses');
    }
};
