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
        Schema::create('tb_master_data_prepresses', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('keterangan_job')->nullable();
            $table->integer('waktu_job')->default(0);
            $table->string('job_rate')->nullable();
            $table->string('point_job')->nullable();
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
        Schema::dropIfExists('tb_master_data_prepresses');
    }
};
