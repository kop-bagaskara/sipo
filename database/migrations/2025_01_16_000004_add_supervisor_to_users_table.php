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
        // Tambahkan kolom supervisor_id ke tabel users jika belum ada
        Schema::connection('pgsql2')->table('users', function (Blueprint $table) {
            if (!Schema::connection('pgsql2')->hasColumn('users', 'supervisor_id')) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('level');
                $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
                $table->index('supervisor_id');
            }
        });

        // Tambahkan kolom untuk role HR
        Schema::connection('pgsql2')->table('users', function (Blueprint $table) {
            if (!Schema::connection('pgsql2')->hasColumn('users', 'is_hr')) {
                $table->boolean('is_hr')->default(false)->after('supervisor_id');
                $table->index('is_hr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('users', function (Blueprint $table) {
            if (Schema::connection('pgsql2')->hasColumn('users', 'is_hr')) {
                $table->dropColumn('is_hr');
            }

            if (Schema::connection('pgsql2')->hasColumn('users', 'supervisor_id')) {
                $table->dropForeign(['supervisor_id']);
                $table->dropColumn('supervisor_id');
            }
        });
    }
};
