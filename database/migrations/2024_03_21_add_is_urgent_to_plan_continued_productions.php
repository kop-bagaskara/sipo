<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('plan_continued_productions')) {
            Schema::table('plan_continued_productions', function (Blueprint $table) {
                if (!Schema::hasColumn('plan_continued_productions', 'is_urgent')) {
                    $table->boolean('is_urgent')->default(false)->after('status');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('plan_continued_productions')) {
            Schema::table('plan_continued_productions', function (Blueprint $table) {
                if (Schema::hasColumn('plan_continued_productions', 'is_urgent')) {
                    $table->dropColumn('is_urgent');
                }
            });
        }
    }
};
