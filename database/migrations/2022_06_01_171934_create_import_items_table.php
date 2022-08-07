<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->bigInteger('import_id');
            $table->string('order_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('condition_code')->nullable();
            $table->string('condition_comment')->nullable();
            $table->date('ship_date')->nullable();
            $table->date('return_date')->nullable();
            $table->boolean('is_valid')->default(true);
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
        Schema::dropIfExists('import_items');
    }
}
