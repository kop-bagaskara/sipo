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
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            // Special materials fields for new products (boolean checkboxes)
            $table->boolean('kertas_khusus')->default(false)->after('material');
            $table->text('kertas_khusus_detail')->nullable()->after('kertas_khusus');
            $table->boolean('tinta_khusus')->default(false)->after('kertas_khusus_detail');
            $table->text('tinta_khusus_detail')->nullable()->after('tinta_khusus');
            $table->boolean('foil_khusus')->default(false)->after('tinta_khusus_detail');
            $table->text('foil_khusus_detail')->nullable()->after('foil_khusus');
            $table->boolean('pale_tooling_khusus')->default(false)->after('foil_khusus_detail');
            $table->text('pale_tooling_khusus_detail')->nullable()->after('pale_tooling_khusus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            $table->dropColumn([
                'kertas_khusus',
                'kertas_khusus_detail',
                'tinta_khusus',
                'tinta_khusus_detail',
                'foil_khusus',
                'foil_khusus_detail',
                'pale_tooling_khusus',
                'pale_tooling_khusus_detail'
            ]);
        });
    }
};
