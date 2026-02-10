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
        Schema::connection('pgsql2')->create('tb_vehicle_asset_requests', function (Blueprint $table) {
            $table->id();
            $table->date('request_date');
            $table->enum('request_type', ['vehicle', 'asset']);
            $table->integer('employee_id');
            $table->string('employee_name');
            $table->string('department');
            $table->integer('divisi_id');
            $table->string('vehicle_type')->nullable(); // for vehicle requests
            $table->string('asset_category')->nullable(); // for asset requests
            $table->string('purpose_type'); // Meeting, Dinas Luar, Training, etc
            $table->text('purpose');
            $table->string('destination'); // Tujuan penggunaan
            $table->string('license_plate')->nullable(); // No. Polisi
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'pending_manager',
                'manager_approved',
                'manager_rejected',
                'hrga_approved',
                'hrga_rejected'
            ])->default('pending_manager');
            
            // Manager approval fields
            $table->integer('manager_id')->nullable();
            $table->text('manager_notes')->nullable();
            $table->timestamp('manager_at')->nullable();
            
            // HRGA approval fields
            $table->integer('hrga_id')->nullable();
            $table->text('hrga_notes')->nullable();
            $table->timestamp('hrga_at')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('employee_id');
            $table->index('divisi_id');
            $table->index('status');
            $table->index('manager_id');
            $table->index('hrga_id');
            $table->index('request_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_vehicle_asset_requests');
    }
};
