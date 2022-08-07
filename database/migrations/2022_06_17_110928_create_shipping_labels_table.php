<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_labels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->string('state')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('shipment_id')->nullable();
            $table->string('owner')->nullable();
            $table->decimal('rate', 12, 2)->default(0)->nullable();
            $table->string('provider')->nullable();
            $table->string('carrier_account')->nullable();
            $table->string('servicelevel_token')->nullable();
            $table->string('servicelevel_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_status')->nullable();
            $table->text('tracking_url_provider')->nullable();
            $table->text('label_url')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->string('parcel')->nullable();
            $table->text('qr_code_url')->nullable();
            $table->text('messages');
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
        Schema::dropIfExists('shipments');
    }
}
