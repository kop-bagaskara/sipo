<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingOppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_meeting_opps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_development_id');
            $table->tinyInteger('meeting_number')->comment('1 = Meeting OPP 1, 2 = Meeting OPP 2');
            $table->date('meeting_date');
            $table->enum('status', ['belum_berjalan', 'berjalan', 'selesai'])->default('belum_berjalan');
            $table->enum('customer_response', ['pending', 'acc', 'reject'])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('marketing_notes')->nullable();
            $table->text('rnd_notes')->nullable();

            // RnD Approval
            $table->enum('rnd_approval', ['pending', 'approve', 'reject'])->default('pending');
            $table->text('rnd_approval_notes')->nullable();
            $table->timestamp('rnd_approved_at')->nullable();
            $table->unsignedBigInteger('rnd_approved_by')->nullable();

            // Marketing Approval
            $table->enum('marketing_approval', ['pending', 'approve', 'reject'])->default('pending');
            $table->text('marketing_approval_notes')->nullable();
            $table->timestamp('marketing_approved_at')->nullable();
            $table->unsignedBigInteger('marketing_approved_by')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('job_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('meeting_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_meeting_opps');
    }
}
