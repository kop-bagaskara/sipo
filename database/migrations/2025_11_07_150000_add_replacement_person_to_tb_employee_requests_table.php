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
        Schema::connection('pgsql2')->table('tb_employee_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('replacement_person_id')->nullable()->after('head_notes');
            $table->string('replacement_person_name')->nullable()->after('replacement_person_id');
            $table->string('replacement_person_nip')->nullable()->after('replacement_person_name');
            $table->string('replacement_person_position')->nullable()->after('replacement_person_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_employee_requests', function (Blueprint $table) {
            $table->dropColumn([
                'replacement_person_id',
                'replacement_person_name',
                'replacement_person_nip',
                'replacement_person_position'
            ]);
        });
    }
};

