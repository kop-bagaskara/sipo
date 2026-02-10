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
        Schema::connection('pgsql2')->table('tb_applicants', function (Blueprint $table) {
            $table->string('ttd_signature')->nullable()->after('ttd_pelamar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_applicants', function (Blueprint $table) {
            $table->dropColumn('ttd_signature');
        });
    }
};
