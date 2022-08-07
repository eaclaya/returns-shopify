<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCollectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_collects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('collect_id')->unsigned()->default(0);
            $table->bigInteger('collection_id')->unsigned()->default(0);
            $table->bigInteger('product_id')->unsigned()->default(0);
            $table->integer('position')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');
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
        Schema::dropIfExists('account_collects');
    }
}
