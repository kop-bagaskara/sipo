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
        Schema::table('tb_label_templates', function (Blueprint $table) {
            $table->longText('html_template')->nullable()->after('field_mapping');
            $table->longText('css_styles')->nullable()->after('html_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_label_templates', function (Blueprint $table) {
            $table->dropColumn(['html_template', 'css_styles']);
        });
    }
};

