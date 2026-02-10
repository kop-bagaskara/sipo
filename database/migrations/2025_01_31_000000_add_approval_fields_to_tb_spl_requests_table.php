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
        Schema::connection('pgsql2')->table('tb_spl_requests', function (Blueprint $table) {
            // Approval fields (mengikuti struktur EmployeeRequest)
            $table->unsignedBigInteger('head_id')->nullable()->after('supervisor_id')->comment('HEAD yang approve');
            $table->timestamp('head_approved_at')->nullable()->after('head_id');
            $table->timestamp('head_rejected_at')->nullable()->after('head_approved_at');
            $table->text('head_notes')->nullable()->after('head_rejected_at');

            $table->unsignedBigInteger('manager_id')->nullable()->after('head_notes')->comment('Manager yang approve');
            $table->timestamp('manager_approved_at')->nullable()->after('manager_id');
            $table->timestamp('manager_rejected_at')->nullable()->after('manager_approved_at');
            $table->text('manager_notes')->nullable()->after('manager_rejected_at');

            $table->unsignedBigInteger('general_id')->nullable()->after('manager_notes')->comment('General Manager yang approve');
            $table->timestamp('general_approved_at')->nullable()->after('general_id');
            $table->timestamp('general_rejected_at')->nullable()->after('general_approved_at');
            $table->text('general_notes')->nullable()->after('general_rejected_at');

            // Rename hrd_id, hrd_approved_at, hrd_rejected_at, hrd_notes untuk konsistensi
            // Tapi karena mungkin sudah ada data, kita biarkan dulu dan gunakan alias di model

            $table->integer('current_approval_order')->default(0)->after('status')->comment('Urutan approval saat ini');

            // Indexes
            $table->index('head_id');
            $table->index('manager_id');
            $table->index('general_id');
            $table->index('current_approval_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->table('tb_spl_requests', function (Blueprint $table) {
            $table->dropIndex(['head_id']);
            $table->dropIndex(['manager_id']);
            $table->dropIndex(['general_id']);
            $table->dropIndex(['current_approval_order']);

            $table->dropColumn([
                'head_id',
                'head_approved_at',
                'head_rejected_at',
                'head_notes',
                'manager_id',
                'manager_approved_at',
                'manager_rejected_at',
                'manager_notes',
                'general_id',
                'general_approved_at',
                'general_rejected_at',
                'general_notes',
                'current_approval_order'
            ]);
        });
    }
};

