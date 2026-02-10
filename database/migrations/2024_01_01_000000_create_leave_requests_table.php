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
        Schema::connection('pgsql2')->create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('leave_type'); // sick, personal, annual, emergency
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('contact_during_leave', 20);
            $table->string('emergency_contact', 20);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamp('submitted_at');
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tb_applicants');
            $table->index(['employee_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('leave_requests');
    }
};
