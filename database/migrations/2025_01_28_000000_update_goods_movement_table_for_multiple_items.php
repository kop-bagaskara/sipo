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
        Schema::table('tb_security_goods_movements', function (Blueprint $table) {
            // Add new JSON column for multiple items
            $table->json('barang_items')->nullable()->after('jenis_movement');

            // Keep old columns for backward compatibility but make them nullable
            $table->text('jenis_barang')->nullable()->change();
            $table->text('deskripsi_barang')->nullable()->change();
            $table->integer('jumlah')->nullable()->change();
            $table->string('satuan')->nullable()->change();
            $table->decimal('berat', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_security_goods_movements', function (Blueprint $table) {
            // Remove the JSON column
            $table->dropColumn('barang_items');

            // Revert old columns to not nullable
            $table->text('jenis_barang')->nullable(false)->change();
            $table->integer('jumlah')->nullable(false)->change();
            $table->string('satuan')->nullable(false)->change();
        });
    }
};
