<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountOrderTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_order_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('transaction_id')->unsigned();
            $table->string('kind')->nullable();
            $table->string('status')->nullable();
            $table->string('gateway')->nullable();
            $table->string('processed_at')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('source_name')->nullable();
            $table->string('currency')->nullable();
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
        Schema::dropIfExists('account_order_transactions');
    }
}
