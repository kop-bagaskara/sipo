<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToMeetingOppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_meeting_opps', function (Blueprint $table) {
            // RnD Approval fields
            $table->enum('rnd_approval', ['pending', 'approve', 'reject'])->default('pending')->after('rnd_notes');
            $table->text('rnd_approval_notes')->nullable()->after('rnd_approval');
            $table->timestamp('rnd_approved_at')->nullable()->after('rnd_approval_notes');
            $table->unsignedBigInteger('rnd_approved_by')->nullable()->after('rnd_approved_at');

            // Marketing Approval fields
            $table->enum('marketing_approval', ['pending', 'approve', 'reject'])->default('pending')->after('rnd_approved_by');
            $table->text('marketing_approval_notes')->nullable()->after('marketing_approval');
            $table->timestamp('marketing_approved_at')->nullable()->after('marketing_approval_notes');
            $table->unsignedBigInteger('marketing_approved_by')->nullable()->after('marketing_approved_at');

            // Add foreign key constraints
            $table->foreign('rnd_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('marketing_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_meeting_opps', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['rnd_approved_by']);
            $table->dropForeign(['marketing_approved_by']);

            // Drop columns
            $table->dropColumn([
                'rnd_approval',
                'rnd_approval_notes',
                'rnd_approved_at',
                'rnd_approved_by',
                'marketing_approval',
                'marketing_approval_notes',
                'marketing_approved_at',
                'marketing_approved_by'
            ]);
        });
    }
}
