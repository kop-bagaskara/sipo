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
        Schema::connection('pgsql2')->table('tb_applicant_test_results', function (Blueprint $table) {
            $table->enum('hrd_status', ['pending', 'approved', 'rejected'])->default('pending')->after('screenshot_path');
            $table->text('hrd_notes')->nullable()->after('hrd_status');
            $table->unsignedBigInteger('hrd_confirmed_by')->nullable()->after('hrd_notes');
            $table->datetime('hrd_confirmed_at')->nullable()->after('hrd_confirmed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_applicant_test_results', function (Blueprint $table) {
            $table->dropColumn(['hrd_status', 'hrd_notes', 'hrd_confirmed_by', 'hrd_confirmed_at']);
        });
    }
};

