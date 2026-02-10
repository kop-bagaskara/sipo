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
        Schema::connection('pgsql2')->create('tb_security_activity_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_log_id')->constrained('tb_security_daily_activity_logs')->onDelete('cascade');
            $table->integer('urutan'); // 1, 2, 3, dst
            $table->time('time_in')->nullable(); // 15:00
            $table->time('time_out')->nullable(); // 15:05
            $table->text('keterangan'); // Giat patroli all area vikon aman fondurif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_security_activity_entries');
    }
};
