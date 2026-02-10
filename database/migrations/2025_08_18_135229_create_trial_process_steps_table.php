<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialProcessStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_trial_process_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trial_sample_id');
            $table->integer('urutan');
            $table->string('proses');
            $table->string('department_terkait');
            $table->date('rencana_trial');
            $table->string('mesin');
            $table->string('status')->default('pending');

            // User yang ditugaskan
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->text('notes')->nullable();
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
        Schema::dropIfExists('tb_trial_process_steps');
    }
}
