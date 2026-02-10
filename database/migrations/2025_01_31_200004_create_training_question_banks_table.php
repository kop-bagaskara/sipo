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
        Schema::connection('pgsql3')->create('tb_training_question_banks', function (Blueprint $table) {
            $table->id();
            $table->text('question'); // Soal
            $table->enum('question_type', [
                'multiple_choice', // Pilihan ganda
                'essay',          // Essay
                'true_false',     // Benar/Salah
                'fill_blank'      // Isian
            ])->default('multiple_choice');
            $table->unsignedBigInteger('difficulty_level_id'); // Kategori kesulitan
            $table->unsignedBigInteger('material_id')->nullable(); // Relasi ke materi (opsional)
            $table->text('correct_answer'); // Jawaban benar
            $table->json('answer_options')->nullable(); // Opsi jawaban untuk pilihan ganda (JSON array)
            $table->text('explanation')->nullable(); // Penjelasan jawaban
            $table->decimal('score', 5, 2)->default(1.00); // Poin/skor soal
            $table->boolean('is_active')->default(true); // Status aktif
            $table->unsignedBigInteger('created_by')->nullable(); // Trainer yang membuat
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('difficulty_level_id')->references('id')->on('tb_training_difficulty_levels')->onDelete('restrict');
            $table->foreign('material_id')->references('id')->on('tb_training_materials')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('difficulty_level_id');
            $table->index('material_id');
            $table->index('is_active');
            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_question_banks');
    }
};

