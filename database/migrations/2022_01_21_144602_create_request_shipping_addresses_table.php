<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestShippingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('request_id')->unsigned();
            $table->string('address_type', 100)->nullable();
            $table->string('zip', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('name')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->string('province', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('country_code', 100)->nullable();
            $table->string('province_code', 100)->nullable();
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
        Schema::dropIfExists('request_shipping_addresses');
    }
}
