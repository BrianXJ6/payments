<?php

use Illuminate\Support\Facades\Route;

// Views
Route::view('', 'web.home')->name('home');

// Gerencianet
Route::prefix('gerencianet')->group(function () {
    Route::view('cartoes', 'web.gerencianet.cart')->name('gn.cart');
    Route::view('boleto', 'web.gerencianet.billet')->name('gn.billet');
    Route::view('pix', 'web.gerencianet.pix')->name('gn.pix');
});
