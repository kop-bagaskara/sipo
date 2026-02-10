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
        Schema::create('tb_development_email_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('process_name'); // Nama proses (Input Awal, Assign ke Prepress, dll)
            $table->string('process_code'); // Kode proses untuk identifikasi
            $table->text('description')->nullable();
            $table->json('recipient_roles'); // Role yang menerima email
            $table->json('reminder_schedule')->nullable(); // H-3, H-2, H-1, dll
            $table->boolean('is_active')->default(true);
            $table->boolean('send_to_rnd_on_every_change')->default(false); // Khusus untuk RnD
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_development_email_notification_settings');
    }
};
