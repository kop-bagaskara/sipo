<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_job_developments', function (Blueprint $table) {
            $table->id();
            $table->string('job_code')->unique(); // JD-YYYYMMDD-XXX
            $table->string('job_name');
            $table->text('specification');
            $table->string('attachment')->nullable();
            $table->integer('estimated_leadtime'); // dalam jam
            $table->enum('status', ['draft', 'planning', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->enum('type', ['proof', 'trial_khusus'])->default('proof');
            $table->foreignId('marketing_user_id')->constrained('users');
            $table->foreignId('rnd_user_id')->nullable()->constrained('users'); // RnD yang handle
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type']);
            $table->index('marketing_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_job_developments');
    }
}
