<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('domain')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('plan_name')->nullable();
            $table->string('owner')->nullable();
            $table->string('customer_email')->nullable();
            $table->boolean('has_discounts')->nullable();
            $table->boolean('has_gift_cards')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['domain','country','currency','plan_name','customer_email','has_discounts','has_gift_cards']);
        });
    }
}
