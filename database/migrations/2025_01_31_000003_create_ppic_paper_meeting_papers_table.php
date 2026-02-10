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
        Schema::create('tb_ppic_paper_meeting_papers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_item_id');
            $table->string('paper_type')->comment('Jenis kertas (contoh: DPC 250, IVORY 230, IVORY SINAR VANDA 220)');
            $table->string('paper_size')->nullable()->comment('Ukuran kertas (contoh: 73 x 52)');
            $table->string('up_count')->nullable()->comment('Jumlah up (contoh: @4 up, @30 up, @63 up)');
            $table->string('paper_code')->nullable()->comment('Kode kertas (contoh: K.060.0250.P LN.087)');
            $table->string('paper_variant')->nullable()->comment('Varian kertas (contoh: IKDP, SPN, IK VR, IK VA)');
            $table->bigInteger('required_quantity')->default(0)->comment('Quantity kertas yang dibutuhkan');

            $table->timestamps();

            // Foreign key
            $table->foreign('meeting_item_id')->references('id')->on('tb_ppic_paper_meeting_items')->onDelete('cascade');

            // Indexes
            $table->index('meeting_item_id');
            $table->index('paper_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meeting_papers');
    }
};

