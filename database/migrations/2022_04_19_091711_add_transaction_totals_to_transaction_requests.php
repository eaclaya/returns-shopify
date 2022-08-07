<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionTotalsToTransactionRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_transactions', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('balance', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_transactions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'balance']);
        });
    }
}
