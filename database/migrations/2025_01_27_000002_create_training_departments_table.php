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
        Schema::create('tb_training_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id'); // ID training
            $table->unsignedBigInteger('department_id'); // ID departemen
            $table->boolean('is_mandatory')->default(false); // Apakah wajib untuk departemen ini
            $table->integer('priority')->default(1); // Prioritas (1 = tinggi, 2 = sedang, 3 = rendah)
            $table->text('notes')->nullable(); // Catatan khusus untuk departemen
            $table->timestamps();

            // Foreign keys
            $table->foreign('training_id')->references('id')->on('tb_training_masters')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('tb_divisis')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['training_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_training_departments');
    }
};
