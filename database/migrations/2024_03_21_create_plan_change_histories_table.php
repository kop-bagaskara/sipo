<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('plan_change_histories')) {
            Schema::create('plan_change_histories', function (Blueprint $table) {
                $table->id();
                $table->string('code_plan');
                $table->date('old_date');
                $table->date('new_date');
                $table->string('old_machine');
                $table->string('new_machine');
                $table->string('change_reason');
                $table->text('notes')->nullable();
                $table->string('changed_by');
                $table->timestamps();

                if (Schema::hasTable('plan_continued_productions')) {
                    $table->foreign('code_plan')
                        ->references('code_plan')
                        ->on('plan_continued_productions')
                        ->onDelete('cascade');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('plan_change_histories');
    }
};
