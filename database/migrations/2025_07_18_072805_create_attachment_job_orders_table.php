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
        Schema::create('tb_attachment_job_orders', function (Blueprint $table) {
            $table->id();
            $table->string('id_job_order');
            $table->string('file_name');
            $table->string('file_type');
            $table->string('file_path');
            $table->string('created_by')->nullable();
            $table->string('changed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_attachment_job_orders');
    }
};
