<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionResultsToProductionSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_production_schedules', function (Blueprint $table) {
            // Production Results
            $table->integer('production_qty')->nullable()->after('quality_notes');
            $table->integer('reject_qty')->nullable()->after('production_qty');
            $table->time('start_time')->nullable()->after('reject_qty');
            $table->time('end_time')->nullable()->after('start_time');
            $table->date('completion_date')->nullable()->after('end_time');
            $table->text('issues_found')->nullable()->after('completion_date');
            $table->text('recommendations')->nullable()->after('issues_found');

            // Revision System
            $table->enum('rnd_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('recommendations');
            $table->text('rnd_approval_notes')->nullable()->after('rnd_approval_status');
            $table->unsignedBigInteger('rnd_approved_by')->nullable()->after('rnd_approval_notes');
            $table->timestamp('rnd_approved_at')->nullable()->after('rnd_approved_by');
            $table->integer('revision_count')->default(0)->after('rnd_approved_at');

            // Foreign key for RND approval
            $table->foreign('rnd_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_production_schedules', function (Blueprint $table) {
            $table->dropForeign(['rnd_approved_by']);
            $table->dropColumn([
                'production_qty',
                'reject_qty',
                'start_time',
                'end_time',
                'completion_date',
                'issues_found',
                'recommendations',
                'rnd_approval_status',
                'rnd_approval_notes',
                'rnd_approved_by',
                'rnd_approved_at',
                'revision_count'
            ]);
        });
    }
}
