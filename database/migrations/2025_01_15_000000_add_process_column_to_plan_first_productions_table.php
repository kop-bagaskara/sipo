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
        Schema::table('tb_plan_first_productions', function (Blueprint $table) {
            $table->string('process')->nullable()->after('flag_status');
            $table->string('material_name')->nullable()->after('process');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_plan_first_productions', function (Blueprint $table) {
            $table->dropColumn(['process', 'material_name']);
        });
    }
};
