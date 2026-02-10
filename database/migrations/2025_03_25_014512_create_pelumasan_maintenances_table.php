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
        Schema::create('tb_pelumasan_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prob')->nullable();
            $table->timestamp('date_prob')->nullable();
            $table->string('mesin')->nullable();
            $table->string('est_day')->nullable();
            $table->string('est_hour')->nullable();
            $table->string('est_min')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelumasan_maintenances');
    }
};
