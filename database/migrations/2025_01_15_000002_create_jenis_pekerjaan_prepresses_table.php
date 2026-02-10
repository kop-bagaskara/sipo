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
        Schema::create('tb_jenis_pekerjaan_prepresses', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_jenis');
            $table->text('keterangan')->nullable();
            $table->integer('waktu_estimasi')->nullable()->comment('dalam menit');
            $table->decimal('job_rate', 10, 2)->nullable();
            $table->integer('point_job')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jenis_pekerjaan_prepresses');
    }
};
