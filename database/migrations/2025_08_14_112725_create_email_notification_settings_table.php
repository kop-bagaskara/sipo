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
        Schema::create('email_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_name'); // Nama notifikasi (e.g., "Input Job Order")
            $table->string('notification_type'); // Tipe notifikasi (e.g., "job_order_prepress")
            $table->text('description')->nullable(); // Deskripsi notifikasi
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });

        // Tabel untuk user yang dapat email untuk setiap setting
        Schema::create('email_notification_user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_notification_setting_id');
            $table->foreignId('user_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Pastikan kombinasi setting_id dan user_id unik
            $table->unique(['email_notification_setting_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_user_settings');
        Schema::dropIfExists('email_notification_settings');
    }
};
