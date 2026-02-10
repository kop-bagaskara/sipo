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
        Schema::connection('pgsql2')->create('tb_employee_data', function (Blueprint $table) {
            $table->id(); // NO (auto increment)
            $table->string('nip', 50)->unique()->index(); // NIP
            $table->string('nama_karyawan', 255); // Nama Karyawan
            $table->string('lp', 10)->nullable(); // LP (Jenis Kelamin atau Level)
            $table->string('lvl', 50)->nullable(); // LVL (Level)
            $table->string('dept', 100)->nullable(); // DEPT (Department)
            $table->string('bagian', 100)->nullable(); // BAGIAN (Section/Division)
            $table->date('tgl_masuk')->nullable(); // TGL MASUK (Entry Date)
            $table->string('status_update', 50)->nullable(); // STATUS UPDATE
            $table->date('tanggal_awal')->nullable(); // TANGGAL AWAL (Start Date)
            $table->date('tanggal_berakhir')->nullable(); // TANGGAL BERAKHIR (End Date)
            $table->string('masa_kerja', 50)->nullable(); // MASA KERJA (Work Period/Years of Service)
            $table->string('tempat_lahir', 100)->nullable(); // TEMPAT LAHIR (Place of Birth)
            $table->date('tgl_lahir')->nullable(); // TGL LAHIR (Date of Birth)
            $table->integer('usia')->nullable(); // USIA (Age)
            $table->text('alamat_ktp')->nullable(); // ALAMAT KTP (ID Card Address)
            $table->string('email', 255)->nullable(); // Email
            $table->string('no_hp', 20)->nullable(); // No HP (Phone Number)
            $table->text('alamat_domisili')->nullable(); // ALAMAT DOMISILI (Domicile Address)
            $table->string('nomor_kontak_darurat', 20)->nullable(); // NOMOR KONTAK DARURAT (Emergency Contact Number)
            $table->string('agama', 50)->nullable(); // AGAMA (Religion)
            $table->string('pendidikan', 100)->nullable(); // PENDIDIKAN (Education)
            $table->string('jurusan', 100)->nullable(); // JURUSAN (Major/Field of Study)
            $table->timestamps();

            // Indexes
            $table->index('nip');
            $table->index('dept');
            $table->index('bagian');
            $table->index('tgl_masuk');
            $table->index('status_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_employee_data');
    }
};

