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
        Schema::table('tb_meeting_opps', function (Blueprint $table) {
            $table->boolean('returned_to_prepress')->default(false)->after('marketing_approval_notes');
            $table->timestamp('returned_to_prepress_at')->nullable()->after('returned_to_prepress');
            $table->text('return_to_prepress_notes')->nullable()->after('returned_to_prepress_at');
            $table->enum('revision_priority', ['urgent', 'normal', 'low'])->nullable()->after('return_to_prepress_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_meeting_opps', function (Blueprint $table) {
            $table->dropColumn([
                'returned_to_prepress',
                'returned_to_prepress_at',
                'return_to_prepress_notes',
                'revision_priority'
            ]);
        });
    }
};
