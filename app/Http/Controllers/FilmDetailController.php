<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FilmDetailController extends Controller
{
    public function filmDay(Request $request) {
        $film = Film::find($request->film_id);
        if ($film) {
            $film_detail_days = DB::table('film_details')
                ->select(DB::raw("DATE_FORMAT(time_start, '%Y-%m-%d') as film_day"))
                ->where('film_id', $film->id)
                ->distinct()
                ->get();
            return $this->responseData($film_detail_days);
        } else {
            return $this->responseMessage("Film not exist");
        }
    }

    public function filmHour(Request $request) {
        $film = Film::find($request->film_id);
        if ($film) {
            if(!$request->film_day) {
                return $this->responseMessage("Field film_day not given");
            }
            $film_detail_hours = DB::table('film_details')
                ->select(DB::raw("DATE_FORMAT(time_start, '%H:%i:%s') as film_hour"))
                ->where('film_id', $film->id)
                ->whereDate('time_start', $request->film_day)
                ->distinct()
                ->get();
            return $this->responseData($film_detail_hours);
        } else {
            return $this->responseMessage("Film not exist");
        }
    }

    public function filmType(Request $request) {
        $film = Film::find($request->film_id);
        if ($film) {
            if(!$request->time_start) {
                return $this->responseMessage("Field time_start not given");
            }
            $film_detail_types = DB::table('film_details')
                ->select('type')
                ->where('film_id', $film->id)
                ->whereDate('time_start', $request->time_start)
                ->distinct()
                ->get();
            return $this->responseData($film_detail_types);
        } else {
            return $this->responseMessage("Film not exist");
        }
    }
}
