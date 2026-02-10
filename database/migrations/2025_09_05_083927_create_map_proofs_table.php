<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapProofsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_map_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_development_id');
            $table->enum('proof_type', ['digital', 'fisik']);
            $table->string('proof_file_path')->nullable();
            $table->enum('customer_response', ['pending', 'acc', 'reject'])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('marketing_notes')->nullable();
            $table->enum('status', ['uploaded', 'sent_to_customer', 'approved', 'rejected'])->default('uploaded');
            $table->timestamp('sent_at')->nullable();
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
        Schema::dropIfExists('tb_map_proofs');
    }
}
