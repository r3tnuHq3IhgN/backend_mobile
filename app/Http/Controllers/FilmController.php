<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    //
    public function comingFilm()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $films = DB::table('films')
            ->where('release_day', '>', now()) 
            ->get();
        return $this->responseData($films, 200);
    }
    public function filmNow()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $films = DB::table('films')
            ->where('release_day', '<=', now()) 
            ->get();
        return $this->responseData($films, 200);
    }

    public function posts(Request $request)
    {
        $film = Film::find($request->film_id);
        if(!$film) {
            return $this->responseMessage('Film not found', 400);
        }
        return $this->responseData($film->posts, 200);
    }
}
