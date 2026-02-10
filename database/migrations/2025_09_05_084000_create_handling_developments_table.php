<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHandlingDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_handling_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_development_id');
            $table->string('action_type'); // 'created', 'sent_to_prepress', 'meeting_opp_1', 'meeting_opp_2', 'scheduling', 'map_proof_upload', 'map_proof_sent', 'sales_order_created', 'status_changed'
            $table->string('action_description');
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->json('action_data')->nullable(); // Data tambahan untuk action
            $table->timestamp('action_time');
            $table->unsignedBigInteger('performed_by');
            $table->string('performed_by_name')->nullable(); // Cache nama user untuk performa
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('job_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['job_development_id', 'action_type']);
            $table->index('action_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_handling_developments');
    }
}
