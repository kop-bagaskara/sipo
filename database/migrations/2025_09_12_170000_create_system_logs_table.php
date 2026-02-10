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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type'); // 'development', 'prepress', 'production', 'general'
            $table->string('action_type'); // 'create', 'update', 'delete', 'status_change'
            $table->string('table_name'); // nama tabel yang diubah
            $table->string('record_id'); // ID record yang diubah
            $table->string('record_identifier')->nullable(); // job_code, nomor_job_order, dll
            $table->json('old_data')->nullable(); // data sebelum perubahan
            $table->json('new_data')->nullable(); // data setelah perubahan
            $table->string('changed_fields')->nullable(); // field yang berubah (comma separated)
            $table->text('description'); // deskripsi perubahan
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_jabatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['log_type', 'created_at']);
            $table->index(['table_name', 'record_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
