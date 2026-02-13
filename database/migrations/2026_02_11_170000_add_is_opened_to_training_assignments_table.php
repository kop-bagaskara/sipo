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
        Schema::connection('pgsql3')->table('tb_training_assignments', function (Blueprint $table) {
            $table->boolean('is_opened')->default(false)->after('status')->comment('Menandai apakah training sudah dibuka oleh penyelenggara');
            $table->timestamp('opened_at')->nullable()->after('is_opened')->comment('Waktu training dibuka oleh penyelenggara');
            $table->index('is_opened');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql3')->table('tb_training_assignments', function (Blueprint $table) {
            $table->dropIndex(['is_opened']);
            $table->dropColumn(['is_opened', 'opened_at']);
        });
    }
};

