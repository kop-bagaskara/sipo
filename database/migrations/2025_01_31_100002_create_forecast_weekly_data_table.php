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
        Schema::create('tb_forecast_weekly_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forecast_item_id')->constrained('tb_forecast_items')->onDelete('cascade');

            // Week Information
            $table->integer('week_number')->comment('Nomor minggu (1, 2, 3, 4, 5)');
            $table->integer('year')->comment('Tahun (2025, 2026)');
            $table->string('week_label')->comment('Label week (W1.2025, W2.2025)');

            // Forecast Data
            $table->decimal('forecast_qty', 15, 2)->nullable()->comment('Forecast QTY per week');
            $table->decimal('forecast_ton', 15, 4)->nullable()->comment('Forecast TON per week');

            // AO Data (Advance Order) - Optional
            $table->decimal('ao_qty', 15, 2)->nullable()->comment('AO QTY per week');
            $table->decimal('ao_ton', 15, 4)->nullable()->comment('AO TON per week');

            // SOD Data (Sales Order Detail) - Optional
            $table->decimal('sod_qty', 15, 2)->nullable()->comment('SOD QTY per week');
            $table->decimal('sod_ton', 15, 4)->nullable()->comment('SOD TON per week');

            $table->timestamps();

            // Indexes
            $table->index('forecast_item_id');
            $table->index(['week_number', 'year']);
            $table->unique(['forecast_item_id', 'week_number', 'year'], 'unique_forecast_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_forecast_weekly_data');
    }
};

