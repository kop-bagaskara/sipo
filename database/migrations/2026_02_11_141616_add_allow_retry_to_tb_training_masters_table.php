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
        Schema::connection('pgsql3')->table('tb_training_masters', function (Blueprint $table) {
            $table->boolean('allow_retry')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->table('tb_training_masters', function (Blueprint $table) {
            $table->dropColumn('allow_retry');
        });
    }
};
