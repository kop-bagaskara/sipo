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
        Schema::connection('pgsql2')->create('tb_applicants', function (Blueprint $table) {
            $table->id();

            // Data Posisi & Jabatan
            $table->string('posisi_dilamar');
            $table->string('gaji_terakhir')->nullable();
            $table->date('mulai_kerja')->nullable();

            // Data Diri
            $table->string('nama_lengkap');
            $table->string('alias')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->string('agama')->nullable();
            $table->string('kebangsaan')->default('Indonesia');
            $table->string('no_ktp')->nullable();

            // Alamat
            $table->text('alamat_ktp')->nullable();
            $table->string('kode_pos_ktp')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('kode_pos_domisili')->nullable();

            // Kontak
            $table->string('no_handphone');
            $table->string('no_npwp')->nullable();
            $table->string('email')->unique();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->string('hubungan_kontak_darurat')->nullable();

            // Data Tambahan
            $table->json('pendidikan')->nullable(); // Array data pendidikan
            $table->json('kursus')->nullable(); // Array data kursus/keterampilan
            $table->json('pengalaman')->nullable(); // Array data pengalaman kerja
            $table->json('keluarga_anak')->nullable(); // Array data keluarga (suami/istri & anak)
            $table->json('keluarga_ortu')->nullable(); // Array data keluarga (orang tua & saudara)
            $table->json('bahasa')->nullable(); // Array kemampuan bahasa
            $table->json('sim')->nullable(); // Array SIM yang dimiliki
            $table->string('punya_mobil')->nullable(); // Ya/Tidak
            $table->string('punya_motor')->nullable(); // Ya/Tidak
            $table->string('kerja_lembur')->nullable(); // Ya/Tidak
            $table->string('kerja_shift')->nullable(); // Ya/Tidak
            $table->string('kerja_luar_kota')->nullable(); // Ya/Tidak
            $table->string('test_psiko')->nullable(); // Ya/Tidak
            $table->string('test_kesehatan')->nullable(); // Ya/Tidak
            $table->string('hobby')->nullable();
            $table->string('lain_lain')->nullable();
            $table->json('referensi')->nullable(); // Array data referensi

            // Deklarasi
            $table->date('tanggal_deklarasi')->nullable();
            $table->string('ttd_pelamar')->nullable();

            // File uploads
            $table->string('cv_file')->nullable();
            $table->string('foto')->nullable();

            // Status & Tracking
            $table->enum('status', ['pending', 'test', 'interview', 'accepted', 'rejected'])->default('pending');
            $table->date('tanggal_melamar');
            $table->boolean('is_draft')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'posisi_dilamar']);
            $table->index('tanggal_melamar');
            $table->index('is_draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_applicants');
    }
};
