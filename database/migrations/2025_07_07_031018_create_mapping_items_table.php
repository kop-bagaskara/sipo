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
        Schema::create('tb_mapping_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->integer('panjang');
            $table->float('lebar', 8, 2);
            $table->float('gramasi', 8, 2);
            $table->float('kg_per_pcs', 8, 4);
            $table->integer('pcs_dc');
            $table->integer('speed');
            $table->integer('target');
            $table->string('tipe_jo');
            $table->string('optimal');
            $table->text('information')->nullable();
            $table->integer('jumlah_t1');
            $table->integer('jumlah_t2');
            $table->integer('jumlah_t3');
            $table->integer('jumlah_t4');
            $table->integer('jumlah_t5');
            $table->integer('jumlah_t6');
            $table->integer('jumlah_t7');
            $table->string('t1');
            $table->string('t2');
            $table->string('t3');
            $table->string('t4');
            $table->string('t5');
            $table->string('t6');
            $table->string('t7');
            $table->string('coating1');
            $table->string('coating2')->nullable();
            $table->string('dimensi1');
            $table->string('dimensi2');
            $table->string('dimensi3');
            $table->string('created_by')->nullable();
            $table->string('changed_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_mapping_items');
    }
};
