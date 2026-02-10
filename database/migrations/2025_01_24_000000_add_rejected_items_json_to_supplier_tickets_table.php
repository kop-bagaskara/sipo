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
        Schema::table('tb_supplier_tickets', function (Blueprint $table) {
            $table->json('rejected_items_json')->nullable()->after('rejection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_supplier_tickets', function (Blueprint $table) {
            $table->dropColumn('rejected_items_json');
        });
    }
};
