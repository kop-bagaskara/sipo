<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuNavigationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('tb_menu_navigation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('menu_key')->unique()->comment('Unique identifier untuk menu (misal: dashboard, hr.portal-training)');
            $table->string('menu_name')->comment('Nama menu yang ditampilkan');
            $table->string('menu_icon')->nullable()->comment('Icon untuk menu');
            $table->string('menu_route')->nullable()->comment('Route name untuk menu');
            $table->json('allowed_divisi')->nullable()->comment('Array divisi yang diizinkan (null = semua divisi)');
            $table->json('allowed_jabatan')->nullable()->comment('Array jabatan yang diizinkan (null = semua jabatan)');
            $table->json('excluded_divisi')->nullable()->comment('Array divisi yang dikecualikan');
            $table->json('excluded_jabatan')->nullable()->comment('Array jabatan yang dikecualikan');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0)->comment('Urutan tampilan menu');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('menu_key');
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql')->dropIfExists('tb_menu_navigation_settings');
    }
}

