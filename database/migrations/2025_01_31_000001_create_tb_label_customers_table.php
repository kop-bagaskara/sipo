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
        Schema::create('tb_label_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique(); // Kode unik customer
            $table->string('customer_name'); // Nama customer
            $table->string('brand_name')->nullable(); // Nama brand (jika berbeda dengan customer name)
            $table->string('contact_person')->nullable(); // Contact person
            $table->string('email')->nullable(); // Email
            $table->string('phone')->nullable(); // Telepon
            $table->text('address')->nullable(); // Alamat
            $table->text('description')->nullable(); // Deskripsi
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_code');
            $table->index('customer_name');
            $table->index('brand_name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_label_customers');
    }
};

