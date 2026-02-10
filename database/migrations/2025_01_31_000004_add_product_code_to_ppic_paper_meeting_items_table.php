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
        Schema::table('tb_ppic_paper_meeting_items', function (Blueprint $table) {
            $table->string('product_code', 50)->nullable()->after('product_name')->comment('Code produk dari mastermaterial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_ppic_paper_meeting_items', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });
    }
};
