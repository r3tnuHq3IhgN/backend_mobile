<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login-vue', function () {
    return view('login');
})->name('login-vue');
Route::get('/check-login', function () {
    return view('check');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');