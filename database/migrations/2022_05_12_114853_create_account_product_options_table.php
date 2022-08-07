<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountProductOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_product_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->index();
            $table->enum('type', ['text', 'color', 'file', 'url'])->default('text');
            $table->string('product_type')->nullable()->index();
            $table->string('option_name')->nullable()->index();
            $table->string('option_value')->nullable()->index();
            $table->string('value')->nullable();
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
        Schema::dropIfExists('account_product_options');
    }
}
