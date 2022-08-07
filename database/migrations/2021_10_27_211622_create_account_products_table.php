<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('product_id')->unsigned()->default(0);
            $table->string('title');
            $table->string('vendor', 100)->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->decimal('price', 12, 2);
            $table->string('sku', 100)->nullable();
            $table->integer('inventory_quantity');
            $table->bigInteger('inventory_item_id')->nullable();
            $table->string('product_type', 100)->nullable();
            $table->boolean('taxable')->default(true);
            $table->string('handle')->nullable();
            $table->string('status', 100)->nullable();
            $table->string('option1_name')->nullable();
            $table->string('option1_value')->nullable();
            $table->string('option2_name')->nullable();
            $table->string('option2_value')->nullable();
            $table->string('option3_name')->nullable();
            $table->string('option3_value')->nullable();
            $table->longText('options')->nullable();
            $table->longText('tags')->nullable();
            $table->longText('images')->nullable();
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
        Schema::dropIfExists('account_products');
    }
}
