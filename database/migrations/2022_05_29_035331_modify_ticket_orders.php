<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTicketOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn('film_price_id');
            $table->integer('film_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn('film_detail_id');
            $table->integer('film_price_id');
        });
    }
}
