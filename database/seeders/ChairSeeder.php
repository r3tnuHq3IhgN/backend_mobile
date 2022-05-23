<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 5; $i++) {
            for($j = 1; $j <= 40; $j++) {
                $chair_names = ["A", "B", "C", "D", "E"];
                $number = $j % 8 != 0 ? $j % 8 : 8;
                $char = $chair_names[floor(($j-1)/8)];
                $type = 2;
                switch($char){
                    case "A":
                        $type = 0;
                        break;
                    case "E":
                        $type = 1;
                        break;
                }
                DB::table('chairs')->insert([
                    "room_id" => $i,
                    "name" => $char . $number,
                    "type" => $type,
                    "status" => 0
                ]);
            }
        }
    }
}
