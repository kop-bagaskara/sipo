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
        Schema::create('tb_supplier_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('po_number'); // PO number from our system (e.g., POM-250920-0001)
            $table->string('supplier_delivery_doc'); // Supplier's delivery document number
            $table->datetime('delivery_date');
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->string('supplier_email')->nullable();
            $table->text('supplier_address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', [
                'pending',
                'approved', 
                'rejected',
                'processed',
                'completed'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // // Foreign key constraints
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // // Indexes
            // $table->index(['status', 'created_at']);
            // $table->index('ticket_number');
            // $table->index('po_number');
            // $table->index('supplier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_supplier_tickets');
    }
};
