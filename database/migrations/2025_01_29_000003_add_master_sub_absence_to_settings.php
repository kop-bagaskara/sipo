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
        Schema::table('tb_master_absence_settings', function (Blueprint $table) {
            $table->json('master_sub_absence')->nullable()->after('description')->comment('JSON untuk sub-kategori absence (contoh: Cuti Khusus categories)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_master_absence_settings', function (Blueprint $table) {
            $table->dropColumn('master_sub_absence');
        });
    }
};
