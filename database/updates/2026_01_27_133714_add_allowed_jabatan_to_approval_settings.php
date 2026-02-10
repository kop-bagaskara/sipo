<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedJabatanToApprovalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_approval_hr_settings', function (Blueprint $table) {
            $table->json('allowed_jabatan')->nullable()->after('role_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_approval_hr_settings', function (Blueprint $table) {
            $table->dropColumn('allowed_jabatan');
        });
    }
}
