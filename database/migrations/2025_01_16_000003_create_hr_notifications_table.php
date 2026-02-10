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
        Schema::connection('pgsql2')->create('tb_hr_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('recipient_id'); // User yang menerima notifikasi
            $table->enum('notification_type', [
                'request_submitted',      // Pengajuan baru diajukan
                'supervisor_approval',    // Menunggu approval atasan
                'hr_approval',           // Menunggu approval HR
                'request_approved',      // Pengajuan disetujui
                'request_rejected',      // Pengajuan ditolak
                'request_cancelled',     // Pengajuan dibatalkan
                'reminder'              // Reminder untuk approval
            ]);
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('tb_employee_requests')->onDelete('cascade');
            $table->index(['recipient_id', 'is_read']);
            $table->index(['notification_type', 'created_at']);
        });

        // Tabel untuk email notification settings HR
        Schema::connection('pgsql2')->create('tb_hr_email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name');
            $table->string('setting_key')->unique();
            $table->text('description')->nullable();
            $table->json('recipient_roles'); // Role yang menerima email
            $table->json('email_template'); // Template email
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_hr_email_settings');
        Schema::connection('pgsql2')->dropIfExists('tb_hr_notifications');
    }
};
