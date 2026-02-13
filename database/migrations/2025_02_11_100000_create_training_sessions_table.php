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
        Schema::connection('pgsql3')->create('tb_training_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id'); // Relasi ke training master
            $table->integer('session_order')->default(1); // Urutan sesi (1, 2, 3, ...)
            $table->string('session_title'); // Judul sesi
            $table->text('description')->nullable(); // Deskripsi sesi
            $table->unsignedBigInteger('difficulty_level_id'); // Tingkat kesulitan (ambil dari tb_training_difficulty_levels)
            $table->string('theme')->nullable(); // Tema/kategori soal (filter dari question_bank)
            $table->integer('question_count')->default(10); // Berapa soal yang diambil dari question bank
            $table->decimal('passing_score', 5, 2)->default(70.00); // Nilai minimum untuk lulus
            $table->boolean('has_video')->default(false); // Apakah sesi ini punya video
            $table->text('video_url')->nullable(); // URL video (jika has_video = true)
            $table->integer('video_duration_seconds')->nullable(); // Durasi video dalam detik
            $table->text('content')->nullable(); // Konten tambahan (text, link, dll)
            $table->boolean('is_active')->default(true); // Status aktif
            $table->integer('display_order')->default(0); // Urutan tampil
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('training_id')
                  ->references('id')
                  ->on('tb_training_masters')
                  ->onDelete('cascade');

            $table->foreign('difficulty_level_id')
                  ->references('id')
                  ->on('tb_training_difficulty_levels')
                  ->onDelete('restrict');

            // Indexes
            $table->index('training_id');
            $table->index('difficulty_level_id');
            $table->index('session_order');
            $table->index('theme');
            $table->index('display_order');
            $table->index('is_active');

            // Unique constraint: satu training tidak boleh punya session_order yang sama
            $table->unique(['training_id', 'session_order'], 'unique_training_session_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_sessions');
    }
};
