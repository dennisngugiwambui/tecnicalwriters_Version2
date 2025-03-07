<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('writers.others.welcome');
})->name('welcome');

Route::get('/failed', function () {
    // Get the user's latest assessment result
    $result = App\Models\AssessmentResult::where('user_id', Auth::id())
        ->latest()
        ->first();
        
    if (!$result) {
        // Fallback if no result exists
        $result = new stdClass();
        $result->percentage = 0;
        $result->created_at = now();
    }
    
    return view('writers.others.failed', compact('result'));
})->name('failed');

Auth::routes();

// Assessment routes (available to authenticated users, regardless of verification status)
Route::middleware(['auth'])->group(function () {
    Route::get('/assessment/grammar', [AssessmentController::class, 'showAssessment'])->name('assessment.grammar');
    Route::post('/assessment/submit', [AssessmentController::class, 'submitAssessment'])->name('assessment.submit');
    Route::post('/assessment/auto-submit', [AssessmentController::class, 'autoSubmitAssessment'])->name('assessment.auto-submit');
});

// Public routes that don't require authentication
Route::get('/order/{id}', [HomeController::class, 'availableOrderDetails'])->name('availableOrderDetails');

// Middleware to check if user is verified
Route::middleware(['auth', 'writer.verified'])->group(function () {
    // Routes that require verified writer status
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/current', [HomeController::class, 'currentOrders'])->name('current');
    Route::get('/bids', [HomeController::class, 'currentBidOrders'])->name('bids');
    Route::get('/revision', [HomeController::class, 'currentOrdersOnRevision'])->name('revision');
    Route::get('/finished', [HomeController::class, 'completedOrders'])->name('finished');
    Route::get('/dispute', [HomeController::class, 'orderOnDispute'])->name('dispute');
    Route::get('/messages', [HomeController::class, 'Messages'])->name('writer.messages');
    Route::get('/finance', [HomeController::class, 'userFinance'])->name('finance');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/statistics', [HomeController::class, 'statistics'])->name('statistics');
    
    // Order management routes
    Route::post('/bid/submit/{id}', [HomeController::class, 'submitBid'])->name('writer.bid.submit');
    Route::post('/file/download', [HomeController::class, 'download'])->name('writer.file.download');
    Route::post('/file/download-multiple', [HomeController::class, 'downloadMultiple'])->name('writer.file.downloadMultiple');
    Route::get('/writer/order/{id}/details', [HomeController::class, 'availableOrderDetails'])->name('writer.order.details');
    Route::post('/writer/order/{id}/message', [HomeController::class, 'sendMessage'])->name('writer.message.send');
    Route::post('/writer/order/upload-files', [HomeController::class, 'uploadFiles'])->name('writer.order.upload');
    Route::post('/writer/order/{id}/mark-messages-read', [HomeController::class, 'markMessagesRead'])->name('writer.order.mark-messages-read');
    Route::get('/order/{id?}', [HomeController::class, 'AssignedOrder'])->name('assigned');
    
    // Message routes
    Route::post('/writer/messages/send', [HomeController::class, 'sendNewMessage'])->name('writer.message.sendNew');
    Route::get('/writer/messages/thread/{orderId}', [HomeController::class, 'viewMessageThread'])->name('writer.message.thread');
    Route::post('/writer/messages/reply', [HomeController::class, 'replyToMessage'])->name('writer.message.reply');
    Route::get('/writer/order/{id}/check-messages', [HomeController::class, 'checkNewMessages'])->name('writer.message.check');
    Route::get('/writer/messages/list', [HomeController::class, 'getMessagesList'])->name('writer.messages.list');
    Route::get('/writer/messages/search', [HomeController::class, 'searchMessages'])->name('writer.messages.search');
});