<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ {
    LoginController,
    ExpenseEntryController,
    EntryController,
    AccountController,
    CategoryController,
    CreditCardController
};

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
Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [LoginController::class, 'currentUser']);
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('categories', [CategoryController::class, 'index']);

    Route::get('entries', [EntryController::class, 'index']);
    Route::delete('entries/{entry}', [EntryController::class, 'destroy']);
    Route::put('entries/{entry}/pay', [EntryController::class, 'pay']);
    Route::put('entries/{entry}/cancel-payment', [EntryController::class, 'cancelPayment']);

    Route::get('accounts', [AccountController::class, 'index']);
    Route::get('credit-cards', [CreditCardController::class, 'index']);

    Route::post('expenses', [ExpenseEntryController::class, 'store']);
});

