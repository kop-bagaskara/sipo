<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('divisi_approval_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->foreignId('spv_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('head_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk performance
            $table->index('divisi_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('divisi_approval_settings');
    }
};
