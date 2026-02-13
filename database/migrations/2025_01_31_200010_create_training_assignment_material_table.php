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
        Schema::connection('pgsql3')->create('tb_training_assignment_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('order')->default(0); // Urutan materi
            $table->timestamps();

            // Foreign keys
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('tb_training_assignments')
                  ->onDelete('cascade');
            
            $table->foreign('material_id')
                  ->references('id')
                  ->on('tb_training_materials')
                  ->onDelete('cascade');

            // Indexes
            $table->index('assignment_id');
            $table->index('material_id');
            $table->index('order');

            // Unique constraint: satu assignment hanya punya satu record per material
            $table->unique(['assignment_id', 'material_id'], 'unique_assignment_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_assignment_material');
    }
};

