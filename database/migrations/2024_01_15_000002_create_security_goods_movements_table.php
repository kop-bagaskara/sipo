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
        Schema::create('tb_security_goods_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut'); // No urut sesuai foto
            $table->date('tanggal'); // Hari/Tanggal
            $table->string('nama_pengunjung'); // Nama pengunjung/pengirim/penerima
            $table->text('alamat')->nullable(); // Alamat
            $table->string('no_telepon')->nullable(); // No telepon
            $table->string('perusahaan_asal')->nullable(); // Perusahaan asal
            
            // Jenis Movement
            $table->enum('jenis_movement', ['masuk', 'keluar']); // Barang masuk atau keluar
            
            // Detail Barang
            $table->text('jenis_barang'); // Jenis barang
            $table->text('deskripsi_barang')->nullable(); // Deskripsi detail barang
            $table->integer('jumlah')->nullable(); // Jumlah barang
            $table->string('satuan')->nullable(); // Satuan (pcs, kg, box, dll)
            $table->decimal('berat', 8, 2)->nullable(); // Berat barang (kg)
            
            // Waktu
            $table->time('jam_masuk')->nullable(); // Jam masuk
            $table->time('jam_keluar')->nullable(); // Jam keluar
            
            // Tujuan/Asal
            $table->text('tujuan')->nullable(); // Tujuan barang (untuk keluar)
            $table->text('asal')->nullable(); // Asal barang (untuk masuk)
            
            // Kendaraan
            $table->string('jenis_kendaraan')->nullable(); // Jenis kendaraan
            $table->string('no_polisi')->nullable(); // Nomor polisi kendaraan
            $table->string('nama_driver')->nullable(); // Nama driver
            
            // Dokumen
            $table->string('no_surat_jalan')->nullable(); // Nomor surat jalan
            $table->string('no_invoice')->nullable(); // Nomor invoice
            $table->text('dokumen_pendukung')->nullable(); // Dokumen lain
            
            // Security & Approval
            $table->string('petugas_security'); // Petugas security yang input
            $table->enum('shift', ['pagi', 'siang', 'malam'])->default('pagi');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->string('approved_by')->nullable(); // Yang menyetujui
            $table->timestamp('approved_at')->nullable(); // Waktu approval
            
            // Catatan
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            $table->text('catatan_security')->nullable(); // Catatan dari security
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tanggal', 'shift']);
            $table->index('jenis_movement');
            $table->index('status');
            $table->index('petugas_security');
            $table->index('nama_pengunjung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_security_goods_movements');
    }
};
