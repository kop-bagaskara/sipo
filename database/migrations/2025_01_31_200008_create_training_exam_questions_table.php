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
        Schema::connection('pgsql3')->create('tb_training_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id'); // Relasi ke exam
            $table->unsignedBigInteger('question_bank_id'); // Soal dari bank soal
            $table->integer('question_order')->default(0); // Urutan soal (random per user)
            $table->text('user_answer')->nullable(); // Jawaban user
            $table->boolean('is_correct')->default(false); // Apakah jawaban benar
            $table->decimal('score_earned', 5, 2)->default(0.00); // Skor yang didapat untuk soal ini
            $table->decimal('max_score', 5, 2)->default(0.00); // Skor maksimal untuk soal ini
            $table->timestamp('answered_at')->nullable(); // Waktu menjawab
            $table->timestamps();

            // Foreign keys
            $table->foreign('exam_id')->references('id')->on('tb_training_exams')->onDelete('cascade');
            $table->foreign('question_bank_id')->references('id')->on('tb_training_question_banks')->onDelete('cascade');

            // Indexes
            $table->index('exam_id');
            $table->index('question_bank_id');
            $table->index('question_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_exam_questions');
    }
};

