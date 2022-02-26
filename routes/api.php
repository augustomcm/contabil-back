<?php

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

Route::middleware(['auth:sanctum'])->get('/user', [\App\Http\Controllers\LoginController::class, 'currentUser']);

Route::post('login', [ \App\Http\Controllers\LoginController::class, 'login']);
Route::post('logout', [ \App\Http\Controllers\LoginController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::post('expenses', [ \App\Http\Controllers\ExpenseEntryController::class, 'store'])
    ->middleware('auth:sanctum');
