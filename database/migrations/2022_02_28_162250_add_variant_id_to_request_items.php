<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantIdToRequestItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->bigInteger('original_variant_id')->nullable();
            $table->bigInteger('new_variant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['original_variant_id', 'new_variant_id']);
        });
    }
}
