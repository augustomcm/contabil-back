<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ExpenseEntryController;

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
Route::post('login', [ LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', [LoginController::class, 'currentUser']);
    Route::post('logout', [LoginController::class, 'logout']);

    Route::post('expenses', [ExpenseEntryController::class, 'store']);
});

