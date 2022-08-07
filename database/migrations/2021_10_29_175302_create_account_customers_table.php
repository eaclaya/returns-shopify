<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->string('email', 100);
            $table->string('first_name', 100)->nullable();
            $table->bigInteger('customer_id');
            $table->string('last_name', 100)->nullable();
            $table->bigInteger('last_order_id')->nullable();
            $table->string('last_order_name', 100)->nullable();
            $table->integer('orders_count')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('tags')->nullable();
            $table->decimal('total_spent', 12, 2)->nullable();
            $table->longText('default_address')->nullable();
            $table->longText('addresses')->nullable();
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
        Schema::dropIfExists('account_customers');
    }
}
