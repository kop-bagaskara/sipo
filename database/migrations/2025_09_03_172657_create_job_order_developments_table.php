<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOrderDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_job_order_developments', function (Blueprint $table) {
            $table->id();
            $table->string('job_code')->unique();
            $table->string('job_name');
            $table->date('tanggal');
            $table->date('job_deadline')->nullable();
            $table->string('customer');
            $table->string('product');
            $table->string('kode_design');
            $table->string('dimension');
            $table->string('material');
            $table->string('total_color');
            $table->json('colors')->nullable(); // untuk menyimpan array warna
            $table->string('qty_order_estimation');
            $table->enum('job_type', ['new', 'repeat']);
            $table->integer('change_percentage')->nullable();
            $table->json('change_details')->nullable(); // untuk menyimpan array detail perubahan
            $table->json('job_order'); // untuk menyimpan array job order
            $table->json('file_data')->nullable(); // untuk menyimpan array file data yang dipilih
            $table->enum('prioritas_job', ['Urgent', 'Normal']);
            $table->json('attachment_paths')->nullable(); // untuk menyimpan array path file attachments
            $table->text('catatan')->nullable();
            $table->string('status_job')->default('OPEN');
            $table->unsignedBigInteger('marketing_user_id');
            $table->timestamps();

            $table->foreign('marketing_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_job_order_developments');
    }
}
