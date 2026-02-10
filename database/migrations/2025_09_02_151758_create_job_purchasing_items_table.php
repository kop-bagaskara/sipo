<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPurchasingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_job_development_purchasing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_development_id')->constrained('job_developments')->onDelete('cascade');
            $table->string('item_name');
            $table->string('supplier_name');
            $table->enum('order_status', ['not_ordered', 'ordered', 'received'])->default('not_ordered');
            $table->date('order_date')->nullable();
            $table->date('received_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('purchasing_user_id')->constrained('users');
            $table->timestamps();

            $table->index(['job_development_id', 'order_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_job_development_purchasing_items');
    }
}
