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
        Schema::connection('pgsql3')->table('tb_training_sessions', function (Blueprint $table) {
            $table->string('google_drive_file_id')->nullable()->after('video_url');
            $table->string('video_source')->default('local')->after('google_drive_file_id'); // 'local' atau 'google_drive'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->table('tb_training_sessions', function (Blueprint $table) {
            $table->dropColumn(['google_drive_file_id', 'video_source']);
        });
    }
};

