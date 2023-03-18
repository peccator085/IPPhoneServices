<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    if (str_starts_with($request->header("Accept"), "x-CiscoIPPhone")) {
        return response()->view("main_xml")->header("Content-Type", "text/xml");
    }
    return view('main_html');
})->name("main");

Route::get('/phonebook/', [\App\Http\Controllers\PhoneBookController::class, 'show']) -> name("phonebook.show");
Route::get("/phonebook/getNameByNumber/{number}", [\App\Http\Controllers\PhoneBookController::class, "getNameByNumber"]);
