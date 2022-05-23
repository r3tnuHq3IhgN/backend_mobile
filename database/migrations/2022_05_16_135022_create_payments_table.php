<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount');
            $table->string('bank_code');
            $table->string('bank_tran_no');
            $table->string('card_type');
            $table->string('order_info');
            $table->dateTime('pay_date');
            $table->string('response_code');
            $table->bigInteger('transaction_no');
            $table->string('transaction_status');
            $table->string('txn_ref');
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
        Schema::dropIfExists('payments');
    }
}
