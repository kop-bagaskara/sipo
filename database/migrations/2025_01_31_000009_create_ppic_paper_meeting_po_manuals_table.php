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
        Schema::create('tb_ppic_paper_meeting_po_manuals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->string('paper_code', 50)->comment('Code kertas');
            $table->string('paper_type', 100)->comment('Type kertas');
            $table->decimal('po_manual_layer_1', 15, 2)->default(0)->comment('BELUM ADA PO Layer 1 (input manual)');
            $table->decimal('po_manual_layer_2', 15, 2)->default(0)->comment('BELUM ADA PO Layer 2 (Qty × 500 × UP)');
            $table->decimal('up_value', 10, 2)->default(5)->comment('UP yang digunakan untuk perhitungan');

            $table->timestamps();

            // Foreign key
            $table->foreign('meeting_id')->references('id')->on('tb_ppic_paper_meetings')->onDelete('cascade');

            // Indexes
            $table->index('meeting_id');
            $table->index('paper_code');

            // Unique constraint: satu meeting, satu paper_code hanya boleh ada satu record
            $table->unique(['meeting_id', 'paper_code'], 'unique_meeting_paper');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_po_manuals');
    }
};
