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
        Schema::connection('pgsql2')->table('tb_security_vehicle_checklists', function (Blueprint $table) {
            // Tambah kolom checklist_pada
            $table->enum('checklist_pada', ['awal_masuk', 'akhir_keluar'])->after('status');

            // Tambah kolom lokasi
            $table->integer('lokasi')->nullable()->after('checklist_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_security_vehicle_checklists', function (Blueprint $table) {
            $table->dropColumn(['checklist_pada', 'lokasi']);
        });
    }
};
