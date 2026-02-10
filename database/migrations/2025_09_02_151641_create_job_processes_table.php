<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_job_development_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_development_id')->constrained('tb_job_developments')->onDelete('cascade');
            $table->string('process_name');
            $table->integer('process_order');
            $table->foreignId('department_id')->constrained('divisi'); // pakai tabel divisi yang sudah ada
            $table->foreignId('assigned_user_id')->constrained('users');
            $table->integer('estimated_duration'); // dalam jam
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['job_development_id', 'process_order']);
            $table->index(['status', 'assigned_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_job_development_processes');
    }
}
