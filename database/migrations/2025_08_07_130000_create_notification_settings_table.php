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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // job_order_prepress, job_order_production, dll
            $table->string('target_type'); // divisi, jabatan, specific_user
            $table->string('target_value'); // PPIC, PREPRESS, HEAD, SPV, atau user_id
            $table->boolean('is_active')->default(true);
            $table->boolean('send_email')->default(true);
            $table->boolean('send_website')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['notification_type', 'target_type', 'target_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
