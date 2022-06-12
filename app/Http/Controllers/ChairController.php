<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChairController extends Controller
{
    public function roomChairs(Request $request) {
        $film_detail = DB::table('film_details')
            ->select(['id', 'room_id'])
            ->where('film_id', $request->film_id)
            ->where('time_start', $request->time_start)
            ->where('type', $request->type)
            ->first();
        $room = Room::where('id', $film_detail->room_id)->first();
        if ($room) {
            $chairs = $room->chairs()->get(['id', 'name', 'type', 'status']);
            $ticket_orders = DB::table('ticket_orders')->where('film_detail_id', $film_detail->id)->get();
            $ticket_order_ids = [];
            forEach($ticket_orders as $ticket_order) {
                $ticket_order_ids[] = $ticket_order->id;
            }
            $booked_chairs = DB::table('chair_orders')->whereIn('ticket_order_id', $ticket_order_ids)->get();
            $chair_ids = [];
            forEach($booked_chairs as $booked_chair) {
                $chair_ids[] = $booked_chair->chair_id;
            }
            forEach($chairs as $chair) {
                if(in_array($chair->id, $chair_ids)) {
                    $chair->status = 1;
                }
            }
            return $this->responseData($chairs, 200);
         } else {
            return $this->responseMessage("Room doesn't exist", 400);
        }
    }
}
