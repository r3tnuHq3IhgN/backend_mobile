<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTicketOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn('food_combo_id');
            $table->dropColumn('chair_id');
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
            $table->integer('food_combo_id');
            $table->integer('chair_id');
        });
    }
}
