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
        Schema::connection('pgsql3')->table('tb_training_assignments', function (Blueprint $table) {
            $table->string('session_code', 50)->nullable()->after('training_id');
            $table->index('session_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->table('tb_training_assignments', function (Blueprint $table) {
            $table->dropIndex(['session_code']);
            $table->dropColumn('session_code');
        });
    }
};

