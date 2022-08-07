<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->string('sku', 100)->nullable();
            $table->string('name')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('title')->nullable();
            $table->text('duties')->nullable();
            $table->boolean('taxable')->nullable();
            $table->integer('quantity');
            $table->boolean('gift_card')->default(0);
            $table->text('tax_lines')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('variant_id')->nullable();
            $table->string('variant_title')->nullable();
            $table->decimal('total_discount', 12, 2)->nullable();
            $table->boolean('requires_shipping')->nullable();
            $table->string('fulfillment_status', 100)->nullable();
            $table->integer('fulfillable_quantity')->nullable();
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
        Schema::dropIfExists('account_order_items');
    }
}
