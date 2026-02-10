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
        Schema::connection('pgsql3')->create('tb_training_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code')->unique(); // Kode materi unik
            $table->string('material_title'); // Judul materi
            $table->text('description')->nullable(); // Deskripsi materi
            $table->unsignedBigInteger('category_id')->nullable(); // Kategori materi
            $table->text('video_path')->nullable(); // Path/URL video
            $table->string('video_resolution')->nullable(); // Resolusi video (480p, 720p, 1080p)
            $table->integer('video_duration_seconds')->nullable(); // Durasi video dalam detik
            $table->integer('display_order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true); // Status aktif
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('created_by')->nullable(); // Trainer yang membuat
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('tb_training_material_categories')->onDelete('set null');
            // Indexes
            $table->index('category_id');
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->dropIfExists('tb_training_materials');
    }
};

