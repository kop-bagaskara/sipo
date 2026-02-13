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
            $table->date('start_date')->nullable()->after('assigned_date');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->table('tb_training_assignments', function (Blueprint $table) {
            $table->dropIndex(['start_date']);
            $table->dropColumn('start_date');
        });
    }
};

