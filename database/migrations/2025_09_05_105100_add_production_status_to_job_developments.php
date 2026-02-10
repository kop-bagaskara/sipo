<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionStatusToJobDevelopments extends Migration
{
    public function up()
    {
        // Update status_job enum to include production statuses
        DB::statement("ALTER TABLE tb_job_order_developments MODIFY COLUMN status_job ENUM(
            'DRAFT',
            'PLANNING',
            'OPEN',
            'IN_PROGRESS',
            'COMPLETED',
            'ASSIGNED_TO_PPIC',
            'FINISH_PREPRESS',
            'MEETING_OPP',
            'READY_FOR_CUSTOMER',
            'REJECTED_BY_MARKETING',
            'REJECTED_BY_CUSTOMER',
            'SALES_ORDER_CREATED',
            'SCHEDULED_FOR_PRODUCTION',
            'IN_PRODUCTION',
            'PRODUCTION_COMPLETED'
        ) DEFAULT 'DRAFT'");
    }

    public function down()
    {
        // Rollback to previous enum values
        DB::statement("ALTER TABLE tb_job_order_developments MODIFY COLUMN status_job ENUM(
            'DRAFT',
            'PLANNING',
            'OPEN',
            'IN_PROGRESS',
            'COMPLETED',
            'ASSIGNED_TO_PPIC',
            'FINISH_PREPRESS',
            'MEETING_OPP',
            'READY_FOR_CUSTOMER',
            'REJECTED_BY_MARKETING',
            'REJECTED_BY_CUSTOMER',
            'SALES_ORDER_CREATED'
        ) DEFAULT 'DRAFT'");
    }
}
