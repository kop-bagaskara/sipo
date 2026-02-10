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
        Schema::create('tb_assign_job_prepresses', function (Blueprint $table) {
            $table->id();
            $table->string('id_job_order')->unique();
            $table->string('id_user_pic');
            $table->string('name_user_pic');
            $table->string('est_waktu_job')->nullable();
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
        Schema::dropIfExists('tb_assign_job_prepresses');
    }
};
