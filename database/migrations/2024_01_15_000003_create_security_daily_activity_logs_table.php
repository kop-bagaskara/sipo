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
        Schema::connection('pgsql2')->create('tb_security_daily_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('hari', 20); // Senin, Selasa, dll
            $table->string('shift', 20); // I (pagi), II (sore), III (malam)
            $table->time('jam_mulai'); // 15:00
            $table->time('jam_selesai'); // 23:00
            $table->text('personil_jaga'); // Masrur + Lidya
            $table->text('kondisi_awal')->nullable(); // Menerima tugas dan tanggung jawab jaga dari Shift 1 (pagi), dengan hari aman
            $table->text('kondisi_akhir')->nullable(); // Serah terima tugas dan tanggung jawab jaga dari Shift sore ke malam sifon aman
            $table->string('menyerahkan_by', 100)->nullable(); // Masrur + Fimero
            $table->string('diterima_by', 100)->nullable(); // Deri
            $table->string('diketahui_by', 100)->nullable(); // Aris
            $table->string('petugas_security', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_security_daily_activity_logs');
    }
};
