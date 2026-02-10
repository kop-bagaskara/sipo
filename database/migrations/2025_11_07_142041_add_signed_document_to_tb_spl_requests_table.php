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
        Schema::connection('pgsql2')->table('tb_spl_requests', function (Blueprint $table) {
            $table->string('signed_document_path')->nullable()->after('hrd_rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_spl_requests', function (Blueprint $table) {
            $table->dropColumn('signed_document_path');
        });
    }
};

