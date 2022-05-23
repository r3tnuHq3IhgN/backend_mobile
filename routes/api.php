<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiUser;
use App\Http\Controllers\ApiVnPay;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\FilmDetailController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\FoodComboController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get('/login', [ApiUser::class, 'login']); 

Route::get('/all-user', [ApiUser::class, 'getAllUser']);

Route::post('/login', [ApiUser::class, 'login']);


Route::post('/register', [ApiUser::class, 'registration']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    //authen
    Route::get('/user', [ApiUser::class, 'getUser']);
    Route::post('/change-pass', [ApiUser::class, 'changePass']);
    Route::post('/check', [ApiUser::class, 'checkLoggerIn']);
    Route::post('/logout', [ApiUser::class, 'logout']);
});
// forgot pass
Route::post('forgot-pass', [ApiUser::class, 'forgotPass']);

//films
Route::get('/coming-film', [FilmController::class, 'comingFilm']);
Route::get('/film-now', [FilmController::class, 'filmNow']);

//vnpay
Route::post('/vnpay-create', [ApiVnPay::class, 'createTransaction']);
Route::get('/vnpay-get', [ApiVnPay::class, 'getData']);

//check phone
Route::post('/check-phone', [ApiUser::class, 'checkPhone']);


Route::get('/film/{film_id}/film-days', [FilmDetailController::class, 'filmDay']);
Route::get('/film/{film_id}/film-hours', [FilmDetailController::class, 'filmHour']);
Route::get('/film/{film_id}/film-types', [FilmDetailController::class, 'filmType']);
Route::get('/chairs', [ChairController::class, 'roomChairs']);
Route::get('/food-combos', [FoodComboController::class, 'index']);
