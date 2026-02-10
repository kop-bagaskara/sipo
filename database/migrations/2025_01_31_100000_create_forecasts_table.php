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
        Schema::create('tb_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('forecast_number')->unique()->comment('Nomor forecast otomatis (contoh: FC-2026-001)');
            $table->string('customer_name')->comment('Nama customer (Unilever, Nabati, OTHERS)');
            $table->string('period_month')->comment('Bulan periode (Januari 2026)');
            $table->string('period_year')->comment('Tahun periode (2026)');

            // Status
            $table->enum('status', [
                'draft',              // Draft
                'submitted',          // Sudah disubmit
                'approved',           // Disetujui
                'rejected',           // Ditolak
                'completed'           // Selesai
            ])->default('draft');

            // Metadata
            $table->unsignedBigInteger('created_by')->nullable()->comment('User yang membuat');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User yang approve');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['customer_name', 'period_month', 'period_year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_forecasts');
    }
};

