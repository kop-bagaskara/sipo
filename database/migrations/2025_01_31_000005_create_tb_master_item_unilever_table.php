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
        Schema::create('tb_master_item_unilever', function (Blueprint $table) {
            $table->id();
            $table->string('KodeDesign')->nullable();
            $table->string('NamaItem')->nullable();
            $table->string('PC')->nullable();
            $table->string('MC')->nullable();
            $table->string('QTY')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_master_item_unilever');
    }
};

