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
        Schema::table('tb_development_email_notification_settings', function (Blueprint $table) {
            // Remove the wrong columns that should be in pivot table
            $table->dropColumn(['setting_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_development_email_notification_settings', function (Blueprint $table) {
            $table->integer('setting_id')->nullable();
            $table->integer('user_id')->nullable();
        });
    }
};
