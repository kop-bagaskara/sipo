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
            $table->string('foto_kondisi')->nullable()->after('status');
            $table->string('foto_dashboard')->nullable()->after('foto_kondisi');
            $table->string('foto_driver')->nullable()->after('foto_dashboard');
            $table->string('foto_lainnya')->nullable()->after('foto_driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_security_vehicle_checklists', function (Blueprint $table) {
            $table->dropColumn(['foto_kondisi', 'foto_dashboard', 'foto_driver', 'foto_lainnya']);
        });
    }
};
