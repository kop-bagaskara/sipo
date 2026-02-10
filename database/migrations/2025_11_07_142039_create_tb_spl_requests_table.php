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
        Schema::connection('pgsql2')->create('tb_spl_requests', function (Blueprint $table) {
            $table->id();
            $table->string('spl_number')->unique(); // Nomor SPL (SPL-YYYYMMDD-XXX)
            $table->date('request_date'); // Tanggal lembur
            $table->string('shift'); // Shift (Pagi, Siang, Malam, dll)
            $table->string('mesin')->nullable(); // Mesin
            $table->text('keperluan'); // Keperluan
            $table->unsignedBigInteger('supervisor_id'); // Supervisor yang membuat
            $table->unsignedInteger('divisi_id'); // Divisi supervisor
            $table->string('status')->default('draft'); // draft, submitted, signed, approved_hrd, rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->unsignedBigInteger('hrd_id')->nullable(); // HRD yang approve
            $table->text('hrd_notes')->nullable();
            $table->timestamp('hrd_approved_at')->nullable();
            $table->timestamp('hrd_rejected_at')->nullable();
            $table->timestamps();

            $table->index(['supervisor_id', 'request_date']);
            $table->index(['divisi_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_spl_requests');
    }
};
