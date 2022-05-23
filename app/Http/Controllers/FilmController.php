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
            ->join('film_details', 'films.id', '=', 'film_details.film_id')
            ->join('film_prices', 'film_details.film_id', '=', 'film_prices.film_details_id')
            ->join('actors', 'films.id', '=', 'actors.film_id')
            ->where('film_details.time_start', '>', now())
            ->whereDate('film_details.time_end', '=', now())
            ->orderBy('film_details.time_start')
            ->get();
        return $this->responseData($films);
    }
    public function filmNow()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $films = DB::table('films')
            ->join('film_details', 'films.id', '=', 'film_details.film_id')
            ->join('film_prices', 'film_details.film_id', '=', 'film_prices.film_details_id')
            ->join('actors', 'films.id', '=', 'actors.film_id')
            ->where('film_details.time_start', '<=', now())
            ->where('film_details.time_end', '>=', now())
            ->orderBy('film_details.time_start')
            ->get();
        return $this->responseData($films);;
    }
}
