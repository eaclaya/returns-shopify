<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundTypeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_type_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('refund_type_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->longText('conditions')->nullable();
            $table->decimal('extra_amount', 12, 2)->default(0);
            $table->enum('action', ['fixed', 'percentage']);
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
        Schema::dropIfExists('refund_type_rules');
    }
}
