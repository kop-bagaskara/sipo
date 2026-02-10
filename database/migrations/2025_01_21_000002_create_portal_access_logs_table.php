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
        Schema::create('portal_access_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('portal_user_id');
            $table->foreign('portal_user_id')->references('id')->on('portal_auth_users')->onDelete('cascade');

            // Action tracking
            $table->enum('action', ['login', 'logout', 'pin_change', 'failed_login', 'lockout', 'unlock']);
            $table->boolean('success')->default(true);

            // Request details
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('details')->nullable(); // JSON details

            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_access_logs');
    }
};
