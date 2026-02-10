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
        Schema::table('tb_job_developments', function (Blueprint $table) {
            $table->enum('priority', ['high', 'medium', 'low'])->after('type')->default('medium');
            $table->string('customer_name', 255)->after('priority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_developments', function (Blueprint $table) {
            $table->dropColumn(['priority', 'customer_name']);
        });
    }
};
