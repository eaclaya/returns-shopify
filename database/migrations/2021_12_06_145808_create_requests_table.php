<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->boolean('confirmed_return')->default(false);
            $table->boolean('confirmed_exchange')->default(false);
            $table->string('transaction_number')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('state_administrator')->nullable();
            $table->string('status_administrator')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->string('order_number')->nullable();
            $table->bigInteger('refund_order_id')->nullable();
            $table->bigInteger('final_order_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('customer_email')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->decimal('total_return', 12, 2)->nullable()->default(0);
            $table->decimal('total_charge', 12, 2)->nullable()->default(0);
            $table->decimal('total_return_original', 12, 2)->nullable();
            $table->decimal('total_return_exchange', 12, 2)->nullable()->default(0);
            $table->decimal('order_credit_amount', 12, 2)->nullable();
            $table->decimal('restocking_fee', 12, 2)->nullable();
            $table->decimal('original_tax_amount', 12, 2)->nullable();
            $table->decimal('original_tax_rate', 12, 2)->nullable();
            $table->decimal('original_grand_total', 12, 2)->nullable();
            $table->decimal('original_subtotal', 12, 2)->nullable();
            $table->string('original_coupon_code')->nullable();
            $table->decimal('original_discount_amount', 12, 2)->nullable();
            $table->decimal('original_shipping_amount', 12, 2)->nullable();
            $table->decimal('restocking_charge', 12, 2)->nullable();
            $table->integer('original_qty')->nullable();
            $table->integer('qty_return')->nullable();
            $table->integer('qty_exchange')->nullable();
            $table->integer('remains_qty')->nullable();
            $table->decimal('new_tax_amount', 12, 2)->nullable();
            $table->decimal('new_tax_rate', 12, 2)->nullable();
            $table->decimal('new_grand_total', 12, 2)->nullable();
            $table->decimal('new_subtotal', 12, 2)->nullable();
            $table->decimal('new_discount_amount')->nullable();
            $table->decimal('new_shipping_amount')->nullable();
            $table->decimal('gift_credit')->nullable();
            $table->string('approval_date')->nullable();
            $table->string('remote_ip')->nullable();
            $table->longText('discrepancy_reason')->nullable();
            $table->longText('refund_reasons')->nullable();
            $table->longText('exchange_reasons')->nullable();
            $table->longText('comments')->nullable();
            $table->longText('payload')->nullable();
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
        Schema::dropIfExists('requests');
    }
}
