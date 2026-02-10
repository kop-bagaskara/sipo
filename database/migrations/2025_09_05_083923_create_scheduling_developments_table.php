<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulingDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_scheduling_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_development_id');
            $table->integer('default_days')->default(14);
            $table->integer('kertas_khusus_days')->default(0);
            $table->integer('foil_khusus_days')->default(0);
            $table->integer('total_estimated_days');
            $table->text('ppic_notes')->nullable();
            $table->text('purchasing_notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('job_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_scheduling_developments');
    }
}
