<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentExampleController;

Route::get('/', function () {
    return view('welcome');
});

// Payment routes
Route::get('/payment/form', [PaymentExampleController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/initialize', [PaymentExampleController::class, 'initializePayment'])->name('payment.initialize');
Route::get('/payment/callback', [PaymentExampleController::class, 'handleCallback'])->name('payment.callback');
Route::get('/payment/mobile-money-instructions', [PaymentExampleController::class, 'mobileMoneyInstructions'])->name('payment.mobile-money-instructions');
Route::get('/payment/check-status', [PaymentExampleController::class, 'checkPaymentStatus'])->name('payment.check-status');
