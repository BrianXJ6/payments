<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Gnet\PixController;
use App\Http\Controllers\API\Gnet\CardController;
use App\Http\Controllers\API\Gnet\BilletController;
use App\Http\Controllers\API\Gnet\CallbackController;

// Gnet group
Route::prefix('payment/gnet')->group(function () {
    Route::post('pix', PixController::class);
    Route::post('card', CardController::class);
    Route::post('billet', BilletController::class);
    Route::post('status', CallbackController::class); // Calbacks para transações normais.
    // Route::post('status/pix', CallbackController::class); // Calbacks para PIX.
});
