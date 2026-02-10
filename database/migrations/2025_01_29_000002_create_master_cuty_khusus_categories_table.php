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
        Schema::create('master_cuty_khusus_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 100)->unique();
            $table->integer('default_duration_days')->default(1)->comment('Durasi default dalam hari');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_cuty_khusus_categories');
    }
};
