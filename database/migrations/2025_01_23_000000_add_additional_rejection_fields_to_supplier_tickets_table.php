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
        Schema::table('tb_supplier_tickets', function (Blueprint $table) {
            $table->decimal('rejected_quantity', 10, 2)->nullable()->after('rejection_reason');
            $table->decimal('accepted_quantity', 10, 2)->nullable()->after('rejected_quantity');
            $table->date('rejection_date')->nullable()->after('accepted_quantity');
            $table->string('grd_number')->nullable()->after('rejection_date');
            $table->string('pqc_number')->nullable()->after('grd_number');
            $table->unsignedBigInteger('grd_created_by')->nullable()->after('pqc_number');
            $table->timestamp('grd_created_at')->nullable()->after('grd_created_by');
            $table->unsignedBigInteger('pqc_created_by')->nullable()->after('grd_created_at');
            $table->timestamp('pqc_created_at')->nullable()->after('pqc_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_supplier_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'rejected_quantity',
                'accepted_quantity', 
                'rejection_date',
                'grd_number',
                'pqc_number',
                'grd_created_by',
                'grd_created_at',
                'pqc_created_by',
                'pqc_created_at'
            ]);
        });
    }
};
