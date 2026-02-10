<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialMaterialSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_trial_material_samples', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->string('tujuan_trial');
            $table->string('material_bahan');
            $table->string('kode_barang');
            $table->text('nama_barang');
            $table->string('kode_supplier');
            $table->string('nama_supplier');
            $table->decimal('jumlah_bahan', 10, 2);
            $table->string('satuan');
            $table->date('tanggal_terima');
            $table->text('deskripsi');
            $table->string('status')->default('draft');

            // User yang membuat pengajuan
            $table->unsignedBigInteger('created_by');

            // User purchasing yang approve/reject
            $table->unsignedBigInteger('purchasing_user_id')->nullable();
            $table->timestamp('purchasing_reviewed_at')->nullable();
            $table->text('purchasing_notes')->nullable();

            // User QA yang verifikasi final
            $table->unsignedBigInteger('qa_user_id')->nullable();
            $table->timestamp('qa_verified_at')->nullable();
            $table->text('qa_notes')->nullable();

            // User yang close
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trial_material_samples');
    }
}
