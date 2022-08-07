<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_steps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id')->unsigned();
            $table->string('name')->nullable();
            $table->tinyInteger('sort_number')->default(1);
            $table->foreign('survey_id')->references('id')->on('surveys');
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
        Schema::dropIfExists('survey_steps');
    }
}
