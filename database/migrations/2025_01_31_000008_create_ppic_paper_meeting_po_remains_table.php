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
        Schema::create('tb_ppic_paper_meeting_po_remains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->string('po_doc_no', 100)->comment('Nomor PO dari database');
            $table->string('paper_code', 50)->comment('Code kertas');
            $table->string('paper_type', 100)->comment('Type kertas');
            $table->decimal('qty_remain', 15, 2)->default(0)->comment('Qty Remain dari PO');
            $table->decimal('po_remain_layer_1', 15, 2)->default(0)->comment('PO Remain Layer 1 (Qty Remain)');
            $table->decimal('po_remain_layer_2', 15, 2)->default(0)->comment('PO Remain Layer 2 (Qty Remain × 500 × UP)');
            $table->decimal('up_value', 10, 2)->default(5)->comment('UP yang digunakan untuk perhitungan');

            $table->timestamps();

            // Foreign key
            $table->foreign('meeting_id')->references('id')->on('tb_ppic_paper_meetings')->onDelete('cascade');

            // Indexes
            $table->index('meeting_id');
            $table->index('po_doc_no');
            $table->index('paper_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_po_remains');
    }
};
