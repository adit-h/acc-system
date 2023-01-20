<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_sale', function (Blueprint $table) {
            $table->id();
            $table->date('trans_date');
            $table->integer('receive_from');
            $table->integer('store_to');
            $table->bigInteger('value');
            $table->bigInteger('sale_id');
            $table->string('reference', 255);
            $table->string('description', 255);
            $table->integer('createby');
            $table->integer('updateby');
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
        Schema::dropIfExists('transaction_sale');
    }
}
