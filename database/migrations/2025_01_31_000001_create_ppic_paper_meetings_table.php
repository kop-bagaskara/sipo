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
        Schema::create('tb_ppic_paper_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_number')->unique()->comment('Nomor meeting otomatis (contoh: PPIC-PM-2025-001)');
            $table->string('customer_name')->comment('Nama customer (contoh: TSPM, UNILEVER, NABATI)');
            $table->string('meeting_month')->comment('Bulan meeting (contoh: September 2025)');
            $table->string('period_month_1', 3)->comment('Bulan pertama periode (OKT, NOV, DES, dll)');
            $table->string('period_month_2', 3)->comment('Bulan kedua periode');
            $table->string('period_month_3', 3)->comment('Bulan ketiga periode');
            $table->decimal('tolerance_percentage', 5, 2)->default(10.00)->comment('Persentase toleransi (default 10%)');

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
            $table->index(['customer_name', 'meeting_month']);
            $table->index('status');
            $table->index('created_at');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ppic_paper_meetings');
    }
};

