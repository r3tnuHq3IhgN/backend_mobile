<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleUrlToFilmPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('film_posts', function (Blueprint $table) {
            $table->string('title');
            $table->string('url');
            $table->dropColumn('name');
            $table->dropColumn('string');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('film_posts', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('url');
            $table->string('name');
            $table->string('string');
        });
    }
}
