<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('film_posts')->insert([
            "title" => "Charlize Theron Introduces the World to [Spoiler] in Behind the Scenes Image From 'Doctor Strange in the Multiverse of Madness'",
            "desc" => "Some magicians do reveal their secrets!",
            "url" => "https://collider.com/doctor-strange-2-multiverse-of-madness-charlize-theron-clea-set-image/",
            "film_id" => 1,
            "image" => "https://static1.colliderimages.com/wordpress/wp-content/uploads/2022/05/doctor-strange-2-multiverse-of-madness-social-featured.jpg?q=50&fit=contain&w=1500&h=&dpr=1.5"
        ]);
    }
}
