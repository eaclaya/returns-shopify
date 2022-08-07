<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_balances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->string('shipping_description')->nullable();
            $table->bigInteger('original_order_id')->unsigned();
            $table->string('original_order_number')->nullable();
            $table->decimal('original_grand_total', 12, 2)->default(0);
            $table->decimal('original_shipping_amount', 12, 2)->default(0);
            $table->decimal('original_shipping_tax_amount', 12, 2)->default(0);
            $table->decimal('original_tax_amount', 12, 2)->default(0);
            $table->string('original_coupon_code')->nullable();
            $table->string('original_discount_description')->nullable();
            $table->decimal('original_gift_credit_amount', 12, 2)->default(0);
            $table->decimal('original_discount_amount', 12, 2)->default(0);
            $table->integer('original_items_qty')->default(0);
            $table->decimal('gift_credit_balance', 12, 2)->default(0);
            $table->decimal('shipping_amount_balance', 12, 2)->default(0);
            $table->decimal('tax_amount_balance', 12, 2)->default(0);
            $table->decimal('total_refunded', 12, 2)->default(0);
            $table->decimal('grand_total_balance', 12, 2)->default(0);
            $table->decimal('shipping_max', 12, 2)->default(0);
            $table->integer('remains_qty')->default(0);
            $table->boolean('cron_executed')->default(0);
            $table->longText('additional_data')->nullable();
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
        Schema::dropIfExists('request_balances');
    }
}
