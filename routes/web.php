<?php

use Illuminate\Support\Facades\Route;

// Views
Route::view('', 'web.home')->name('home');

// Gerencianet
Route::prefix('gerencianet')->group(function () {
    Route::view('cartoes', 'web.gerencianet.cart')->name('gn.cart');
});
