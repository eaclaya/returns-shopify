<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPriceRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_price_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('rule_id')->unsigned();
            $table->string('value_type', 100)->nullable();
            $table->decimal('value');
            $table->string('customer_selection', 12, 2)->nullable();
            $table->string('target_type', 100)->nullable();
            $table->string('target_selection', 100)->nullable();
            $table->string('allocation_method', 100)->nullable();
            $table->integer('allocation_limit');
            $table->boolean('once_per_customer');
            $table->integer('usage_limit');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->text('entitled_product_ids')->nullable();
            $table->text('entitled_variant_ids')->nullable();
            $table->text('entitled_collection_ids')->nullable();
            $table->text('entitled_country_ids')->nullable();
            $table->text('prerequisite_product_ids')->nullable();
            $table->text('prerequisite_variant_ids')->nullable();
            $table->text('prerequisite_collection_ids')->nullable();
            $table->text('prerequisite_saved_search_ids')->nullable();
            $table->text('prerequisite_customer_ids')->nullable();
            $table->text('prerequisite_subtotal_range')->nullable();
            $table->text('prerequisite_quantity_range')->nullable();
            $table->text('prerequisite_shipping_price_range')->nullable();
            $table->text('prerequisite_to_entitlement_quantity_ratio')->nullable();
            $table->text('prerequisite_to_entitlement_purchase')->nullable();
            $table->string('title', 100)->nullable();
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
        Schema::dropIfExists('account_price_rules');
    }
}
