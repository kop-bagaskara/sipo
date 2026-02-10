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
        Schema::table('tb_ppic_paper_meeting_papers', function (Blueprint $table) {
            $table->string('paper_name', 255)->nullable()->after('paper_code')->comment('Nama kertas lengkap dari mastermaterial');
            $table->decimal('zgsm', 10, 2)->nullable()->after('paper_name')->comment('ZGSM dari mastermaterial untuk perhitungan TON');
            $table->decimal('zlength', 10, 2)->nullable()->after('zgsm')->comment('ZLength dari mastermaterial');
            $table->decimal('zwidth', 10, 2)->nullable()->after('zlength')->comment('ZWidth dari mastermaterial');
            $table->decimal('up_value', 10, 2)->nullable()->default(5)->after('up_count')->comment('UP (Unit Per) per jenis kertas');
            $table->string('cover_sampai', 10)->nullable()->after('up_value')->comment('Bulan COVER SAMPAI (JAN, FEB, MAR, dll)');
            $table->decimal('minus_paper_pcs', 15, 2)->nullable()->default(0)->after('cover_sampai')->comment('MINUS PAPER dalam PCS');
            $table->decimal('minus_paper_rim', 15, 6)->nullable()->default(0)->after('minus_paper_pcs')->comment('MINUS PAPER dalam RIM');
            $table->decimal('minus_paper_ton', 15, 6)->nullable()->default(0)->after('minus_paper_rim')->comment('MINUS PAPER dalam TON');
            $table->decimal('total_kebutuhan_ton', 15, 6)->nullable()->default(0)->after('minus_paper_ton')->comment('TOTAL KEBUTUHAN KERTAS (TON) - hanya nilai negatif (kekurangan)');
            $table->text('catatan')->nullable()->after('total_kebutuhan_ton')->comment('Catatan per jenis kertas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_ppic_paper_meeting_papers', function (Blueprint $table) {
            $table->dropColumn([
                'paper_name',
                'zgsm',
                'zlength',
                'zwidth',
                'up_value',
                'cover_sampai',
                'minus_paper_pcs',
                'minus_paper_rim',
                'minus_paper_ton',
                'total_kebutuhan_ton',
                'catatan'
            ]);
        });
    }
};
