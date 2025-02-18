<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/current', [App\Http\Controllers\HomeController::class,'currentOrders'])->name('curret');

Route::get('/bids', [App\Http\Controllers\HomeController::class,'currentBidOrders'])->name('bids');
