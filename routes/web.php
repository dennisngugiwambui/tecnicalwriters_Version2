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

Route::get('/messages', [App\Http\Controllers\HomeController::class,'Messages'])->name('writer.messages');


Route::get('/finance', [App\Http\Controllers\HomeController::class,'userFinance'])->name('finance');


Route::get('/profile', [App\Http\Controllers\HomeController::class,'profile'])->name('profile');

Route::get('/statistics', [App\Http\Controllers\HomeController::class,'statistics'])->name('statistics');



Route::get('/order/201394828', [App\Http\Controllers\HomeController::class,'AssignedOrder'])->name('assigned');

// routes/web.php
Route::get('/order/{id}', [App\Http\Controllers\HomeController::class, 'availableOrderDetails'])->name('availableOrderDetails');


Route::post('/bid/submit/{id}', [App\Http\Controllers\HomeController::class, 'submitBid'])->name('writer.bid.submit');

Route::post('/file/download', [App\Http\Controllers\HomeController::class, 'download'])->name('writer.file.download');
Route::post('/file/download-multiple', [App\Http\Controllers\HomeController::class, 'downloadMultiple'])->name('writer.file.downloadMultiple');

Route::post('/writer/messages/send', [App\Http\Controllers\HomeController::class, 'sendNewMessage'])->name('writer.message.sendNew');
Route::get('/writer/messages/thread/{orderId}', [App\Http\Controllers\HomeController::class, 'viewMessageThread'])->name('writer.message.thread');
Route::post('/writer/messages/reply', [App\Http\Controllers\HomeController::class, 'replyToMessage'])->name('writer.message.reply');
Route::get('/writer/order/{id}/check-messages', [App\Http\Controllers\HomeController::class, 'checkNewMessages'])->name('writer.message.check');


Route::get('/writer/order/{id}/details', [App\Http\Controllers\HomeController::class, 'availableOrderDetails'])->name('writer.order.details');
Route::post('/writer/order/{id}/message', [App\Http\Controllers\HomeController::class, 'sendMessage'])->name('writer.message.send');

// File upload and management routes
Route::post('/writer/order/upload-files', [App\Http\Controllers\HomeController::class, 'uploadFiles'])->name('writer.order.upload');
Route::post('/writer/order/{id}/mark-messages-read', [App\Http\Controllers\HomeController::class, 'markMessagesRead'])->name('writer.order.mark-messages-read');

// Dynamic assigned order viewing (instead of hardcoded 201394828)
Route::get('/order/{id?}', [App\Http\Controllers\HomeController::class, 'AssignedOrder'])->name('assigned');

// Add these routes to routes/web.php

// Get messages list for AJAX updates
Route::get('/writer/messages/list', [App\Http\Controllers\HomeController::class, 'getMessagesList'])->name('writer.messages.list');

// Search messages
Route::get('/writer/messages/search', [App\Http\Controllers\HomeController::class, 'searchMessages'])->name('writer.messages.search');

