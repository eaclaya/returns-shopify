<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->index();
            $table->bigInteger('request_id')->index();
            $table->bigInteger('order_id')->index()->nullable();
            $table->string('order_number')->index()->nullable();
            $table->decimal('amount_to_refund', 12, 2)->nullable()->default(0.0);
            $table->decimal('credits_to_refund', 12, 2)->nullable()->default(0.0);
            $table->enum('order_relation', ['current', 'parent', 'other'])->default('current');
            $table->string('refund_method')->nullable();
            $table->integer('refund_method_id')->nullable();
            $table->string('currency')->nullable();
            $table->bigInteger('transaction_id')->nullable();
            $table->enum('status', ['executed', 'not_executed'])->default('not_executed');
            $table->string('gateway')->nullable();
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
        Schema::dropIfExists('request_transactions');
    }
}
