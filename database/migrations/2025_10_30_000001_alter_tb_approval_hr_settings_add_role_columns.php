<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_approval_hr_settings', function (Blueprint $table) {
            $table->string('approver_type')->default('user')->comment('user | role')->after('approval_order');
            $table->string('role_key')->nullable()->comment('e.g., hr')->after('approver_type');
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_approval_hr_settings', function (Blueprint $table) {
            $table->dropColumn(['approver_type', 'role_key']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};


