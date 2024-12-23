<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentsWebhook;
use App\Http\Controllers\Customers;
use App\Http\Controllers\Refunds;
use App\Http\Controllers\Balance;
use App\Http\Controllers\BalanceTransactions;
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
header('Access-Control-Allow-Origin: *');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect-payment', function () {
    return view('redirect-payment');
});

Route::get('/checkout', function () {
    return view('checkout');
});

Route::post('/payment-intents', [PaymentController::class, 'createPaymentIntent']);
Route::get('/payment-intents/{id}', [PaymentController::class, 'retrievePaymentIntent']);
Route::get('/payment-intents', [PaymentController::class, 'listPaymentIntent']);
Route::post('/payment-intents/{id}/capture', [PaymentController::class, 'capturePaymentIntent']);
Route::post('/payment-intents/{id}/cancel', [PaymentController::class, 'cancelPaymentIntent']);
Route::post('/create-confirm-intent', [PaymentController::class, 'createConfirmPaymentIntent']);

Route::post('/customers', [Customers::class, 'createCustomer']);

Route::post('/events', [PaymentsWebhook::class, 'paymentsListener']);

Route::post('/refunds', [Refunds::class, 'createRefund']);

Route::get('/balance/{id}', [Balance::class, 'retrieveBalance']);

Route::get('/balance_transactions', [BalanceTransactions::class, 'listAllBalanceTransactions']);
