<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_catalogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->default(1);
            $table->string('handle');
            $table->string('title')->nullable();
            $table->longText('body', 200)->nullable();
            $table->string('vendor', 100)->nullable();
            $table->string('custom_product', 100)->nullable();
            $table->longText('tags', 200)->nullable();
            $table->string('published')->nullable();
            $table->string('option1_name')->nullable();
            $table->string('option1_value')->nullable();
            $table->string('option2_name')->nullable();
            $table->string('option2_value', 6)->nullable();
            $table->string('option3_name')->nullable();
            $table->string('option3_value')->nullable();
            $table->string('variant_sku', 100)->nullable();
            $table->string('variant_inventory_tracker', 100)->nullable();
            $table->integer('variant_inventory')->nullable();
            $table->string('variant_inventory_policy')->nullable();
            $table->string('variant_fulfillment_service', 100)->nullable();
            $table->decimal('variant_price', 12, 2)->nullable();
            $table->string('variant_requires_shipping')->nullable();
            $table->string('variant_taxable')->nullable();
            $table->string('variant_barcode', 100)->nullable();
            $table->text('img_src', 100)->nullable();
            $table->integer('image_position')->nullable();
            $table->string('gift_card')->nullable();
            $table->string('status', 100)->nullable();

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
        Schema::dropIfExists('account_catalogs');
    }
}
