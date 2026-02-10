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
            $table->timestamp('assigned_to_ppic_at')->nullable()->after('marketing_user_id');
            $table->unsignedBigInteger('assigned_to_ppic_by')->nullable()->after('assigned_to_ppic_at');
            $table->foreign('assigned_to_ppic_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_ppic_by']);
            $table->dropColumn(['assigned_to_ppic_at', 'assigned_to_ppic_by']);
        });
    }
};
