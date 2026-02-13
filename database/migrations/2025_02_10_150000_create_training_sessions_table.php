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
        Schema::connection('pgsql2')->create('tb_training_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id'); // Relasi ke training master
            $table->integer('session_order')->default(1); // Urutan sesi (1, 2, 3, ...)
            $table->string('session_title'); // Judul sesi
            $table->text('description')->nullable(); // Deskripsi sesi
            $table->boolean('has_video')->default(false); // Apakah sesi ini punya video
            $table->text('video_url')->nullable(); // URL video (jika has_video = true)
            $table->integer('video_duration_seconds')->nullable(); // Durasi video dalam detik
            $table->text('content')->nullable(); // Konten tambahan (text, link, dll)
            $table->boolean('is_active')->default(true); // Status aktif
            $table->integer('display_order')->default(0); // Urutan tampil
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('training_id')
                  ->references('id')
                  ->on('tb_training_masters')
                  ->onDelete('cascade');

            // Indexes
            $table->index('training_id');
            $table->index('session_order');
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
        Schema::connection('pgsql2')->dropIfExists('tb_training_sessions');
    }
};

