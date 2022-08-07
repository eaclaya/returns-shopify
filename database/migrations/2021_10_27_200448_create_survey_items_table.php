<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id')->unsigned();
            $table->bigInteger('survey_step_id')->unsigned();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->string('label', 100)->nullable();
            $table->string('value', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->tinyInteger('sort_number')->default(1);
            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('survey_step_id')->references('id')->on('survey_steps');
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
        Schema::dropIfExists('survey_items');
    }
}
