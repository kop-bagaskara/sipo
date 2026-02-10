<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_handling_job_prepresses', function (Blueprint $table) {
            $table->id();
            $table->integer('id_job_order');
            $table->string('status_handling');
            $table->timestamp('date_handling')->nullable();
            $table->string('notify_priority')->nullable();
            $table->string('id_user_handle')->nullable();
            $table->string('name_user_handle')->nullable();
            $table->string('created_by')->nullable();
            $table->string('changed_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_handling_job_prepresses');
    }
};
