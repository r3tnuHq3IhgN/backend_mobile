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
        $chair_names = ["A", "B", "C", "D", "E"];
        for($room = 1; $room <= 5; $room++) {
            for($row = 0; $row <= 4; $row++) {
                if($row == 0) {
                    $type = 0;
                } else if($row == 4) {
                    $type = 1;
                } else {
                    $type = 2;
                }
                for($col = 0; $col <= 7; $col++) {
                    DB::table('chairs')->insert([
                        "room_id" => $room,
                        "row" => $row,
                        "col" => $col,
                        "name" => $chair_names[$row] . ($col + 1),
                        "type" => $type,
                        "status" => 0
                    ]);
                }
            }
        }
    }
}
