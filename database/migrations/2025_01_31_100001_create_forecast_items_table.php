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
        Schema::create('tb_forecast_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forecast_id')->constrained('tb_forecasts')->onDelete('cascade');

            // Item Information
            $table->string('material_code')->nullable()->comment('Kode material dari mastermaterial');
            $table->string('design_code')->nullable()->comment('Kode design (DS.0230.0092)');
            $table->string('item_name')->comment('Nama item produk');
            $table->text('remarks')->nullable()->comment('Catatan (By PO, By PO + Forecast, dll)');
            $table->string('dpc_group')->nullable()->comment('Grouping DPC (DPC 310 42,5 x 83 / 2 up)');

            // Summary Data (calculated from weekly data)
            $table->decimal('forecast_qty', 15, 2)->default(0)->comment('Total Forecast QTY');
            $table->decimal('forecast_ton', 15, 4)->default(0)->comment('Total Forecast TON');
            $table->decimal('ao_qty', 15, 2)->default(0)->comment('Total AO QTY');
            $table->decimal('ao_ton', 15, 4)->default(0)->comment('Total AO TON');
            $table->decimal('sod_qty', 15, 2)->default(0)->comment('Total SOD QTY');
            $table->decimal('sod_ton', 15, 4)->default(0)->comment('Total SOD TON');

            $table->integer('sort_order')->default(0)->comment('Urutan tampil');
            $table->timestamps();

            // Indexes
            $table->index('forecast_id');
            $table->index('material_code');
            $table->index('design_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_forecast_items');
    }
};

