<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestGiftcards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_giftcards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->string('order_number', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('balance', 12, 2)->nullable();
            $table->string('currency', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expired_at')->nullable();
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
        Schema::dropIfExists('request_giftcards');
    }
}
