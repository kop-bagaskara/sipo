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
        Schema::connection('pgsql2')->create('tb_applicant_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->string('test_type'); // test_1, test_2, test_3, test_4, test_5
            $table->string('test_name');
            $table->integer('score');
            $table->integer('max_score');
            $table->json('answers')->nullable(); // Store test answers
            $table->datetime('test_date');
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['applicant_id', 'test_type']);
            $table->index('test_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql2')->dropIfExists('tb_applicant_test_results');
    }
};
