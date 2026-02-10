<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingOppStatusToJobDevelopments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            // Add new status values for meeting OPP flow
            $table->enum('status_job', [
                'DRAFT',
                'PLANNING',
                'OPEN',
                'IN_PROGRESS',
                'FINISH_PREPRESS',
                'MEETING_OPP',
                'READY_FOR_CUSTOMER',
                'REJECTED_BY_MARKETING',
                'COMPLETED',
                'SALES_ORDER_CREATED'
            ])->default('DRAFT')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_job_order_developments', function (Blueprint $table) {
            // Revert to original status values
            $table->enum('status_job', [
                'DRAFT',
                'PLANNING',
                'OPEN',
                'IN_PROGRESS',
                'COMPLETED'
            ])->default('DRAFT')->change();
        });
    }
}
