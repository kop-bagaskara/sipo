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
        // Tabel untuk shift kerja
        Schema::connection('pgsql2')->create('tb_work_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name'); // Pagi, Siang, Malam
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel untuk jenis ketidakhadiran - sesuai form "PERMOHONAN TIDAK MASUK KERJA"
        Schema::connection('pgsql2')->create('tb_absence_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name'); // Dinas, Cuti Tahunan, Cuti Khusus, Cuti Haid, Cuti Hamil, Ijin
            $table->text('description')->nullable();
            $table->boolean('requires_medical_certificate')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel untuk kendaraan/inventaris
        Schema::connection('pgsql2')->create('tb_company_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number'); // Nomor kendaraan
            $table->string('vehicle_type'); // Motor, Mobil, dll
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('license_plate')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Tabel untuk inventaris perusahaan
        Schema::connection('pgsql2')->create('tb_company_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->string('asset_type'); // Laptop, Printer, dll
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Tabel untuk tracking penggunaan kendaraan/inventaris
        Schema::connection('pgsql2')->create('tb_asset_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->enum('asset_type', ['vehicle', 'inventory']);
            $table->unsignedBigInteger('asset_id'); // ID dari vehicle atau asset
            $table->unsignedBigInteger('employee_id');
            $table->date('usage_date');
            $table->date('return_date')->nullable();
            $table->text('usage_purpose');
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('tb_employee_requests')->onDelete('cascade');
            $table->index(['employee_id', 'status']);
            $table->index(['usage_date', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_asset_usage_logs');
        Schema::connection('pgsql2')->dropIfExists('tb_company_assets');
        Schema::connection('pgsql2')->dropIfExists('tb_company_vehicles');
        Schema::connection('pgsql2')->dropIfExists('tb_absence_types');
        Schema::connection('pgsql2')->dropIfExists('tb_work_shifts');
    }
};
