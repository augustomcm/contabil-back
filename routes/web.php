<?php

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

    $entry = new \App\Models\Entry([
        'value' => new \App\Helpers\Money(10000),
        'type' => \App\Models\EntryType::DEFAULT
    ]);

//    return view('welcome');
});