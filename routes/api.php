<?php

use App\Http\Controllers\ApiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/login', [ApiUser::class, 'login']);

Route::get('/check', [ApiUser::class, 'checkLoggerIn'])->middleware('auth:api'); 

Route::post('/register', [ApiUser::class, 'registration']);
Route::post('/logout', [ApiUser::class, 'logout'])->middleware('auth:api');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
