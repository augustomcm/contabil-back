<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ {
    LoginController,
    ExpenseEntryController,
    EntryController,
    AccountController,
    CategoryController,
    CreditCardController,
    IncomeController
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
    Route::get('entries/financial-statement', [EntryController::class, 'financialStatement']);

    Route::get('accounts', [AccountController::class, 'index']);
    Route::post('accounts', [AccountController::class, 'store']);
    Route::put('accounts/{account}', [AccountController::class, 'update']);

    Route::get('credit-cards', [CreditCardController::class, 'index']);
    Route::post('credit-cards', [CreditCardController::class, 'store']);
    Route::put('credit-cards/{creditCard}', [CreditCardController::class, 'update']);
    Route::put('credit-cards/{creditCard}/close-invoice', [CreditCardController::class, 'closeInvoice']);
    Route::put('credit-cards/{creditCard}/pay-invoice', [CreditCardController::class, 'payInvoice']);

    Route::post('expenses', [ExpenseEntryController::class, 'store']);
    Route::post('incomes', [IncomeController::class, 'store']);
});

