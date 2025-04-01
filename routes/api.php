<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FlutterwaveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Flutterwave Payment Routes
Route::prefix('flutterwave')->group(function () {
    Route::post('/initialize', [FlutterwaveController::class, 'initialize']);
    Route::get('/verify/{reference}', [FlutterwaveController::class, 'verify']);
    Route::post('/webhook', [FlutterwaveController::class, 'handleWebhook']);
});
