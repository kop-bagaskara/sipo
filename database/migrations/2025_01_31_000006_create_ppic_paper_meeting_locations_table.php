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
        Schema::create('tb_ppic_paper_meeting_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->string('location_code', 50)->comment('Code dari masterlocation');
            $table->string('location_name', 255)->nullable()->comment('Name dari masterlocation');
            $table->integer('sort_order')->default(0)->comment('Urutan tampil');

            $table->timestamps();

            // Foreign key
            $table->foreign('meeting_id')->references('id')->on('tb_ppic_paper_meetings')->onDelete('cascade');

            // Indexes
            $table->index('meeting_id');
            $table->index('location_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_locations');
    }
};
