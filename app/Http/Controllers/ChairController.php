<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChairController extends Controller
{
    public function roomChairs(Request $request) {
        $room_id = DB::table('film_details')
            ->select('room_id')
            ->where('film_id', $request->film_id)
            ->whereDate('time_start', $request->time_start)
            ->where('type', $request->type)
            ->first()
            ->room_id;
        $room = Room::find($room_id);
        if ($room) {
            return $this->responseData($room->chairs()->get(['name', 'type', 'status']), 200);
        } else {
            return $this->responseMessage("Room doesn't exist", 400);
        }
    }
}
