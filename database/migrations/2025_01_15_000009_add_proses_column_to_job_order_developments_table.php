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
            $table->json('proses')->nullable()->after('job_type')->comment('Array of production processes in JSON format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            $table->dropColumn('proses');
        });
    }
};
