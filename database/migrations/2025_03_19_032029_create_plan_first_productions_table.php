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
        Schema::create('tb_plan_first_productions', function (Blueprint $table) {
            $table->id();
            $table->string('code_plan');
            $table->string('code_item');
            $table->string('code_machine');
            $table->string('quantity');
            $table->string('up_cetak')->nullable();
            $table->string('capacity');
            $table->double('est_jam');
            $table->double('est_day');
            $table->timestamp('start_jam')->nullable();
            $table->timestamp('end_jam')->nullable();
            $table->string('flag_status')->nullable();
            $table->string('wo_docno')->nullable();
            $table->string('so_docno')->nullable();
            $table->string('delivery_date')->nullable();
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
        Schema::dropIfExists('tb_plan_first_productions');
    }
};
