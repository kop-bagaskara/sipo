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
        Schema::create('tb_ppic_paper_meeting_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->unsignedBigInteger('location_id');
            $table->string('paper_code', 50)->comment('Code kertas dari mastermaterial');
            $table->string('paper_type', 100)->comment('Type kertas');
            $table->decimal('stock_layer_1', 15, 2)->default(0)->comment('Total Stok Layer 1');
            $table->decimal('stock_layer_2', 15, 2)->default(0)->comment('Total Stok Layer 2');
            $table->decimal('stock_layer_3', 15, 2)->default(0)->comment('Total Stok Layer 3');

            $table->timestamps();

            // Foreign keys
            $table->foreign('meeting_id')->references('id')->on('tb_ppic_paper_meetings')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('tb_ppic_paper_meeting_locations')->onDelete('cascade');

            // Indexes
            $table->index('meeting_id');
            $table->index('location_id');
            $table->index('paper_code');

            // Unique constraint: satu meeting, satu location, satu paper_code hanya boleh ada satu record
            $table->unique(['meeting_id', 'location_id', 'paper_code'], 'unique_meeting_location_paper');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_stocks');
    }
};
