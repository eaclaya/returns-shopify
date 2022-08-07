<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGiftcardIdToRequestGiftcards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_giftcards', function (Blueprint $table) {
            $table->bigInteger('giftcard_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_giftcards', function (Blueprint $table) {
            $table->dropColumn(['giftcard_id']);
        });
    }
}
