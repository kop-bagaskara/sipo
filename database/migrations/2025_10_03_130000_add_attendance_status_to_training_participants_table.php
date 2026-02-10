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
        Schema::table('tb_training_participants', function (Blueprint $table) {
            // Add attendance_status field
            $table->enum('attendance_status', ['present', 'absent', 'late'])->nullable()->after('registration_status');

            // Add index for performance
            $table->index(['attendance_status', 'registration_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_training_participants', function (Blueprint $table) {
            $table->dropIndex(['attendance_status', 'registration_status']);
            $table->dropColumn('attendance_status');
        });
    }
};
