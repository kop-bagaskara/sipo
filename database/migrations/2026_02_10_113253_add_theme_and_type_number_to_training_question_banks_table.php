<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThemeAndTypeNumberToTrainingQuestionBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql3')->table('tb_training_question_banks', function (Blueprint $table) {
            $table->string('theme')->nullable()->after('material_id'); // Tema soal (misal: "Pembeda Iso 9001:2015")
            $table->integer('type_number')->nullable()->after('theme'); // Nomor tipe soal (1-5 untuk TIPE 1, TIPE 2, dll)
            
            // Index untuk pencarian berdasarkan tema dan tipe
            $table->index('theme');
            $table->index('type_number');
            $table->index(['material_id', 'theme', 'type_number']); // Composite index untuk grouping
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql3')->table('tb_training_question_banks', function (Blueprint $table) {
            $table->dropIndex(['material_id', 'theme', 'type_number']);
            $table->dropIndex(['type_number']);
            $table->dropIndex(['theme']);
            $table->dropColumn(['theme', 'type_number']);
        });
    }
}
