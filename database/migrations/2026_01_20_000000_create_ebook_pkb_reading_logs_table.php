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
        Schema::create('tb_ebook_pkb_reading_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('start_page')->default(1);
            $table->integer('last_page_viewed')->default(1);
            $table->integer('total_pages_viewed')->default(0);
            $table->integer('time_spent_seconds')->default(0); // Total waktu membaca dalam detik
            $table->timestamp('session_start_at')->useCurrent();
            $table->timestamp('session_end_at')->nullable();
            $table->text('pages_visited')->nullable(); // JSON array of pages visited
            $table->text('interaction_log')->nullable(); // JSON array of interactions
            $table->boolean('marked_as_complete')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('session_start_at');
            $table->index('marked_as_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ebook_pkb_reading_logs');
    }
};
