<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('account_id')->unsigned();
            $table->string('order_number', 100);
            $table->string('name', 100);
            $table->string('closed_at', 100)->nullable();
            $table->string('currency', 100)->nullable();
            $table->decimal('current_total_discounts', 12, 2)->nullable();
            $table->decimal('current_total_price', 12, 2)->nullable();
            $table->decimal('current_subtotal_price', 12, 2)->nullable();
            $table->decimal('current_total_tax', 12, 2)->nullable();
            $table->bigInteger('customer_id')->unsigned();
            $table->string('email', 100)->nullable();
            $table->string('financial_status', 100)->nullable();
            $table->string('fulfillment_status', 100)->nullable();
            $table->string('gateway', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('processed_at', 100)->nullable();
            $table->string('processing_method', 100)->nullable();
            $table->decimal('subtotal_price', 12, 2)->nullable();
            $table->decimal('total_weight', 12, 2)->nullable();
            $table->decimal('total_tax', 12, 2)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('total_outstanding', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->decimal('total_line_items_price', 12, 2)->nullable();
            $table->decimal('total_discounts', 12, 2)->nullable();
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
        Schema::dropIfExists('account_orders');
    }
}
