<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_production_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_development_id');
            $table->date('production_date');
            $table->time('production_time');
            $table->string('machine_name');
            $table->string('machine_code');
            $table->enum('status', ['scheduled', 'ready', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('production_notes')->nullable();
            $table->text('quality_notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('job_order_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['production_date', 'machine_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_production_schedules');
    }
}
