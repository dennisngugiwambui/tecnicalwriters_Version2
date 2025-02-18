<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/current', [App\Http\Controllers\HomeController::class,'currentOrders'])->name('curret');

Route::get('/bids', [App\Http\Controllers\HomeController::class,'currentBidOrders'])->name('bids');

Route::get('/revision', [App\Http\Controllers\HomeController::class,'currentOrdersOnRevision'])->name('revision');

Route::get('/finished', [App\Http\Controllers\HomeController::class,'completedOrders'])->name('finished');

Route::get('/dispute', [App\Http\Controllers\HomeController::class,'orderOnDispute'])->name('dispute');

Route::get('/messages', [App\Http\Controllers\HomeController::class,'Messages'])->name('messages');
