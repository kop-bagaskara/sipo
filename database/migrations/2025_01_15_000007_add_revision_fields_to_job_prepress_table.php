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
        Schema::table('tb_job_prepress', function (Blueprint $table) {
            $table->boolean('is_revision')->default(false)->after('status');
            $table->unsignedBigInteger('original_job_id')->nullable()->after('is_revision');
            $table->text('revision_notes')->nullable()->after('original_job_id');
            $table->enum('revision_priority', ['urgent', 'normal', 'low'])->nullable()->after('revision_notes');
            $table->timestamp('revision_requested_at')->nullable()->after('revision_priority');

            // Add foreign key constraint
            $table->foreign('original_job_id')->references('id')->on('tb_job_order_developments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_job_prepress', function (Blueprint $table) {
            $table->dropForeign(['original_job_id']);
            $table->dropColumn([
                'is_revision',
                'original_job_id',
                'revision_notes',
                'revision_priority',
                'revision_requested_at'
            ]);
        });
    }
};
