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
            // Tambah kolom foto
            $table->string('foto_kondisi')->nullable()->after('status');
            $table->string('foto_dashboard')->nullable()->after('foto_kondisi');
            $table->string('foto_driver')->nullable()->after('foto_dashboard');
            $table->string('foto_lainnya')->nullable()->after('foto_driver');

            // Update kolom yang sudah ada untuk required fields
            $table->string('no_polisi')->nullable(false)->change(); // Make required
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_security_vehicle_checklists', function (Blueprint $table) {
            $table->dropColumn(['foto_kondisi', 'foto_dashboard', 'foto_driver', 'foto_lainnya']);
            $table->string('no_polisi')->nullable()->change(); // Revert to nullable
        });
    }
};
