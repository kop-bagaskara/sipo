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
        // Pivot table di pgsql3 untuk menghubungkan TrainingMaster dengan TrainingMaterial
        Schema::connection('pgsql3')->create('tb_training_master_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id'); // TrainingMaster di pgsql3
            $table->unsignedBigInteger('material_id'); // TrainingMaterial di pgsql3
            $table->integer('display_order')->default(0); // Urutan materi dalam training
            $table->timestamps();

            // Foreign keys (semua di pgsql3)
            $table->foreign('training_id')->references('id')->on('tb_training_masters')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('tb_training_materials')->onDelete('cascade');
            
            // Indexes
            $table->index('training_id');
            $table->index('material_id');
            $table->index('display_order');
            
            // Unique constraint: satu training hanya punya satu record per material
            $table->unique(['training_id', 'material_id'], 'unique_training_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_master_material');
    }
};

