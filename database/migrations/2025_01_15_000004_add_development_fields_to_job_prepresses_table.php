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
        Schema::table('tb_job_prepresses', function (Blueprint $table) {
            $table->boolean('assigned_from_development')->default(false)->after('received_by');
            $table->unsignedBigInteger('development_job_id')->nullable()->after('assigned_from_development');
            $table->foreign('development_job_id')->references('id')->on('tb_job_order_developments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_prepresses', function (Blueprint $table) {
            $table->dropForeign(['development_job_id']);
            $table->dropColumn(['assigned_from_development', 'development_job_id']);
        });
    }
};
