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
        Schema::create('tb_security_vehicle_checklists', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut'); // No urut sesuai foto
            $table->date('tanggal'); // Hari/Tanggal
            $table->string('nama_driver'); // Nama Driver
            $table->string('model_kendaraan'); // Model (seperti di foto: Daihatsu, P. Jumaredin, dll)
            $table->time('jam_out')->nullable(); // Out (jam keluar)
            $table->time('jam_in')->nullable(); // In (jam masuk)
            $table->decimal('bbm_awal', 8, 2)->nullable(); // BBM Awal
            $table->decimal('bbm_akhir', 8, 2)->nullable(); // BBM Akhir
            $table->integer('km_awal')->nullable(); // KM Awal
            $table->integer('km_akhir')->nullable(); // KM Akhir
            $table->text('tujuan')->nullable(); // Tujuan
            $table->text('keterangan')->nullable(); // Ket (Keterangan)

            // Additional fields for better tracking
            $table->string('no_polisi')->nullable(); // Nomor polisi kendaraan
            $table->string('petugas_security'); // Petugas security yang input
            $table->enum('shift', ['pagi', 'siang', 'malam'])->default('pagi');
            $table->enum('status', ['keluar', 'masuk', 'selesai'])->default('keluar');

            $table->timestamps();

            // Indexes
            $table->index(['tanggal', 'shift']);
            $table->index('nama_driver');
            $table->index('no_polisi');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_security_vehicle_checklists');
    }
};
