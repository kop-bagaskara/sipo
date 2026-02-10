<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialWorkflowHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_trial_workflow_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trial_sample_id');
            $table->unsignedBigInteger('user_id');

            // Action yang dilakukan
            $table->string('action');

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional data jika diperlukan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_trial_workflow_history');
    }
}
