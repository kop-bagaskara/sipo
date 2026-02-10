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
        Schema::connection('pgsql2')->create('tb_math_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question'); // Soal matematika
            $table->string('answer'); // Jawaban benar (bisa multiple format: "47", "1 1/4,1.25,5/4")
            $table->integer('question_number')->unique(); // Nomor soal (1, 2, 3, ...)
            $table->string('question_type')->default('pattern'); // pattern, calculation, conversion, word_problem
            $table->integer('difficulty_level')->default(1); // Level kesulitan (1-5)
            $table->text('explanation')->nullable(); // Penjelasan jawaban (opsional)
            $table->boolean('is_active')->default(true); // Aktif/tidak aktif
            $table->integer('display_order')->default(0); // Urutan tampil
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'display_order']);
            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_math_questions');
    }
};

