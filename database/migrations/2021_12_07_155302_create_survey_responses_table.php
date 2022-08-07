<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id')->unsigned();
            $table->bigInteger('request_id')->unsigned()->nullable();
            $table->bigInteger('return_item_id')->unsigned();
            $table->bigInteger('survey_item_id')->unsigned();
            $table->string('step_one')->nullable();
            $table->string('step_two')->nullable();
            $table->string('step_three')->nullable();
            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->softDeletes();
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
        Schema::dropIfExists('survey_responses');
    }
}
