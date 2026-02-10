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
        Schema::create('portal_auth_users', function (Blueprint $table) {
            $table->id();

            // Link to main users table
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Portal authentication credentials
            $table->string('portal_pin'); // bcrypt hashed
            $table->timestamp('pin_changed_at')->nullable();
            $table->boolean('pin_changed_on_first_login')->default(false);

            // Portal access status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('last_login_user_agent')->nullable();

            // Portal session management
            $table->string('portal_session_id')->nullable()->unique();
            $table->timestamp('portal_session_expires_at')->nullable();

            // Security fields
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Administration
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index('portal_session_id');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_auth_users');
    }
};
