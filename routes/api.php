<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Gnet\CardController;
use App\Http\Controllers\API\Gnet\BilletController;
use App\Http\Controllers\API\Gnet\CallbackController;

// Gnet group
Route::prefix('payment/gnet')->group(function () {
    Route::post('card', CardController::class);
    Route::post('billet', BilletController::class);
    Route::post('status', CallbackController::class);
});
