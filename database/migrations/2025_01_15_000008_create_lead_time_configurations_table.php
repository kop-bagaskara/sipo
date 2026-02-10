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
        Schema::create('tb_lead_time_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_development_id');
            $table->integer('tinta_material_days')->default(7);
            $table->integer('kertas_baru_days')->default(30);
            $table->integer('foil_days')->default(7);
            $table->integer('tooling_days')->default(14);
            $table->decimal('produksi_hours', 8, 2)->default(1.0);
            $table->integer('total_lead_time_days')->default(0);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('job_order_development_id')->references('id')->on('job_order_developments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lead_time_configurations');
    }
};
