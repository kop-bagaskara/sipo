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
        Schema::connection('pgsql2')->table('tb_employee_data', function (Blueprint $table) {
            $table->string('foto_path', 255)->nullable()->after('jurusan'); // Foto karyawan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_employee_data', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });
    }
};

