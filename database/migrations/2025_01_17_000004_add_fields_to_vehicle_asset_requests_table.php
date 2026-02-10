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
        Schema::connection('pgsql2')->table('tb_vehicle_asset_requests', function (Blueprint $table) {
            $table->string('purpose_type')->after('asset_category'); // Meeting, Dinas Luar, Training, etc
            $table->string('destination')->after('purpose'); // Tujuan penggunaan
            $table->string('license_plate')->nullable()->after('destination'); // No. Polisi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_vehicle_asset_requests', function (Blueprint $table) {
            $table->dropColumn(['purpose_type', 'destination', 'license_plate']);
        });
    }
};
