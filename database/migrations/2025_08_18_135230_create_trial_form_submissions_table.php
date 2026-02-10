<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialFormSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_trial_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trial_process_step_id');
            $table->unsignedBigInteger('user_id');

            // Data form yang diisi user
            $table->json('form_data'); // Flexible form data dalam format JSON
            $table->text('notes')->nullable();
            $table->text('conclusion')->nullable(); // Kesimpulan dari user

            // Status submission
            $table->string('status')->default('draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_trial_form_submissions');
    }
}
