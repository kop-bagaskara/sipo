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
        Schema::create('tb_holiday_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // Tanggal override
            $table->enum('override_type', ['holiday', 'working_day', 'half_day', 'custom_hours']); // Type override
            $table->decimal('working_hours', 4, 2)->nullable(); // Jam kerja custom (NULL = ikut default)
            $table->string('description', 255)->nullable(); // Keterangan override (misal: "Libur Nasional", "Sabtu Minggu Masuk")
            $table->boolean('is_active')->default(true); // Untuk enable/disable override
            $table->unsignedBigInteger('created_by')->nullable(); // User yang membuat override
            $table->unsignedBigInteger('updated_by')->nullable(); // User yang update terakhir
            $table->timestamps();

            // Indexes
            $table->index(['date', 'is_active']);
            $table->index('override_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_holiday_days');
    }
};
