<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('request_id')->unsigned();
            $table->string('transaction_type')->nullable();
            $table->bigInteger('original_order_id')->nullable();
            $table->bigInteger('order_item_id')->nullable();
            $table->bigInteger('original_product_id')->nullable();
            $table->string('original_sku')->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('original_discount', 12, 2)->nullable();
            $table->decimal('original_row_total', 12, 2)->nullable();
            $table->decimal('original_tax_amount', 12, 2)->nullable();
            $table->integer('original_qty')->nullable();
            $table->string('original_name')->nullable();
            $table->longText('original_image')->nullable();
            $table->bigInteger('new_product_id')->nullable();
            $table->string('new_sku')->nullable();
            $table->decimal('new_price', 12, 2)->nullable();
            $table->decimal('new_discount', 12, 2)->nullable();
            $table->decimal('new_tax_amount', 12, 2)->nullable();
            $table->string('new_name')->nullable();
            $table->longText('new_image')->nullable();
            $table->integer('new_qty')->nullable();
            $table->decimal('new_row_total', 12, 2)->nullable();
            $table->integer('qty_return')->nullable();
            $table->integer('qty_exchange')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');
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
        Schema::dropIfExists('request_items');
    }
}
