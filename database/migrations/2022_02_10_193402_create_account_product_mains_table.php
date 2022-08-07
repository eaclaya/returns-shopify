<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountProductMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_product_mains', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('product_id')->unsigned()->default(0);
            $table->string('title');
            $table->string('sku', 100)->nullable();
            $table->string('product_type', 100)->nullable();
            $table->string('handle')->nullable();
            $table->string('status', 100)->nullable();
            $table->longText('tags')->nullable();
            $table->longText('image')->nullable();
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
        Schema::dropIfExists('account_product_mains');
    }
}
