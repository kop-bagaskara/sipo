<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialPurchasingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_material_purchasing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_development_id');
            $table->enum('material_type', ['kertas', 'tinta', 'foil', 'pale_tooling']);
            $table->text('material_detail')->nullable();
            $table->enum('purchasing_status', ['belum', 'sudah'])->default('belum');
            $table->text('purchasing_info')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            // $table->timestamp('updated_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('job_order_development_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['job_order_development_id', 'material_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_material_purchasing');
    }
}
