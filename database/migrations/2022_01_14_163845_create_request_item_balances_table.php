<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_item_balances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('original_order_id')->unsigned();
            $table->string('original_order_number')->nullable();
            $table->bigInteger('item_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('variant_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->string('product_type')->nullable();
            $table->string('sku')->nullable();
            $table->string('sku_variant')->nullable();
            $table->string('name')->nullable();
            $table->integer('qty_ordered')->default(1);
            $table->integer('qty_balance')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('row_total', 12, 2)->default(0);
            $table->decimal('total_refunded', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_balance', 12, 2)->default(0);
            $table->decimal('tax_balance', 12, 2)->default(0);
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
        Schema::dropIfExists('request_item_balances');
    }
}
