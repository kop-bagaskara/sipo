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
        Schema::connection('pgsql2')->table('tb_security_goods_movements', function (Blueprint $table) {
            $table->json('barang_keluar_items')->nullable()->after('barang_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_security_goods_movements', function (Blueprint $table) {
            $table->dropColumn('barang_keluar_items');
        });
    }
};
