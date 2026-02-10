<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobQualityChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_job_development_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_development_id')->constrained('job_developments')->onDelete('cascade');
            $table->date('check_date');
            $table->enum('result', ['pass', 'fail'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('qc_user_id')->constrained('users');
            $table->timestamps();

            $table->index(['job_development_id', 'result']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_job_development_quality_checks');
    }
}
