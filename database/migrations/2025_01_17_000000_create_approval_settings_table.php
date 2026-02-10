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
        Schema::create('tb_approval_hr_settings', function (Blueprint $table) {
            $table->id();
            $table->string('request_type')->comment('Jenis pengajuan: shift_change, absence, overtime, etc');
            $table->string('approval_level')->comment('Level approval: supervisor, hr, manager, etc');
            $table->integer('approval_order')->comment('Urutan approval: 1, 2, 3, etc');
            $table->unsignedBigInteger('user_id')->comment('ID user yang berhak approve');
            $table->string('user_name')->comment('Nama user yang berhak approve');
            $table->string('user_position')->nullable()->comment('Jabatan user');
            $table->boolean('is_active')->default(true)->comment('Status aktif/tidak');
            $table->text('description')->nullable()->comment('Deskripsi approval level');
            $table->timestamps();

            $table->index(['request_type', 'approval_level']);
            $table->index(['request_type', 'approval_order']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_approval_hr_settings');
    }
};
