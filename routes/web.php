<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FileController;
use App\Http\Controllers\WriterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('writers.others.welcome');
})->name('welcome');

Route::get('/failed', [AssessmentController::class, 'showFailedPage'])->name('failed');


Auth::routes();

// Assessment routes (available to authenticated users, regardless of verification status)
Route::middleware(['auth'])->group(function () {
    Route::get('/assessment/grammar', [AssessmentController::class, 'showAssessment'])->name('assessment.grammar');
    Route::post('/assessment/submit', [AssessmentController::class, 'submitAssessment'])->name('assessment.submit');
    Route::post('/assessment/auto-submit', [AssessmentController::class, 'autoSubmitAssessment'])->name('assessment.auto-submit');
});

// Profile setup routes - accessible after passing assessment but before accessing main system
Route::middleware(['auth'])->group(function () {
    Route::get('/profilesetup', [ProfileSetupController::class, 'showProfileSetup'])->name('profilesetup');
    Route::post('/profilesetup/submit', [ProfileSetupController::class, 'saveProfileSetup'])->name('profilesetup.submit');
});

// Public routes that don't require authentication
Route::get('/order/{id}', [HomeController::class, 'availableOrderDetails'])->name('availableOrderDetails');

// Apply auth middleware only - we'll handle verification in the controllers
Route::middleware(['auth'])->group(function () {
    // Redirect to home route which will be handled by the HomeController
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/writers', [HomeController::class, 'index'])->name('writers.index');
    
    // Writer routes
    Route::get('/current', [HomeController::class, 'currentOrders'])->name('current');
    Route::get('/bids', [HomeController::class, 'currentBidOrders'])->name('bids');
    Route::get('/revision', [HomeController::class, 'currentOrdersOnRevision'])->name('revision');
    Route::get('/finished', [HomeController::class, 'completedOrders'])->name('finished');
    Route::get('/current', [HomeController::class, 'currentOrders'])->name('current');
    Route::get('/dispute', [HomeController::class, 'orderOnDispute'])->name('dispute');
    Route::get('/messages', [HomeController::class, 'Messages'])->name('writer.messages');
    Route::get('/finance', [HomeController::class, 'userFinance'])->name('finance');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/statistics', [HomeController::class, 'statistics'])->name('statistics');
    
    // Available Orders page (where writers go after completing profile)
    Route::get('/available', [HomeController::class, 'index'])->name('writer.available');
    
    // Order management routes
    Route::post('/bid/submit/{id}', [HomeController::class, 'submitBid'])->name('writer.bid.submit');
    Route::post('/writer/order/{id}/confirm', [App\Http\Controllers\HomeController::class, 'confirmAssignment'])->name('writer.confirm.assignment');
    Route::post('/file/download-multiple', [HomeController::class, 'downloadMultiple'])->name('writer.file.downloadMultiple');
    Route::get('/writer/order/{id}/details', [HomeController::class, 'availableOrderDetails'])->name('writer.order.details');
    Route::post('/writer/order/{id}/message', [HomeController::class, 'sendMessage'])->name('writer.message.send');
    Route::post('/writer/order/upload-files', [HomeController::class, 'uploadFiles'])->name('writer.order.upload');
    Route::post('/writer/order/{id}/mark-messages-read', [HomeController::class, 'markMessagesRead'])->name('writer.order.mark-messages-read');
    Route::get('/writer/order/{id}', [HomeController::class, 'AssignedOrder'])->name('assigned');
    
    // Message routes
    Route::post('/writer/messages/send', [HomeController::class, 'sendNewMessage'])->name('writer.message.sendNew');
    Route::post('/writer/messages/send', [HomeController::class, 'sendNewMessage'])->name('writer.send.message');
    Route::get('/writer/messages/thread/{orderId}', [HomeController::class, 'viewMessageThread'])->name('writer.message.thread');
    Route::post('/writer/messages/reply', [HomeController::class, 'replyToMessage'])->name('writer.message.reply');
    Route::get('/writer/order/{id}/check-messages', [HomeController::class, 'checkNewMessages'])->name('writer.message.check');
    Route::get('/writer/messages/list', [HomeController::class, 'getMessagesList'])->name('writer.messages.list');
    Route::get('/writer/messages/search', [HomeController::class, 'searchMessages'])->name('writer.messages.search');

    Route::post('/writer/order/{id}/confirm', [App\Http\Controllers\HomeController::class, 'confirmAssignment'])->name('writer.confirm.assignment');
    Route::post('/writer/order/{id}/reject', [App\Http\Controllers\HomeController::class, 'rejectAssignment'])->name('writer.reject.assignment');
    

    // Add these routes inside the auth middleware group
    // File Management for Writers
     // File Management - updated for consistency
     Route::post('/writer/order/upload-files', [HomeController::class, 'uploadFiles'])->name('writer.order.upload');
     Route::get('/writer/order/{id}/files', [HomeController::class, 'orderFiles'])->name('writer.order.files');
     
     // Use FileController for downloads to keep code DRY
     Route::get('/writer/file/{id}/download', [FileController::class, 'writerDownload'])->name('writer.file.download');
     Route::post('/writer/file/download-multiple', [FileController::class, 'writerDownloadMultiple'])->name('writer.file.download-multiple');
    // File upload routes
   // Route::get('/upload/modal/{orderId}', [UploadController::class, 'showUploadModal'])->name('upload.modal');
   // Route::post('/upload/file', [UploadController::class, 'uploadFile'])->name('upload.file');
    //Route::post('/upload/submit', [UploadController::class, 'submitFinalWork'])->name('upload.submit');
    //Route::delete('/upload/{uploadId}', [UploadController::class, 'deleteUpload'])->name('upload.delete');
   // Route::get('/upload/download/{uploadId}', [UploadController::class, 'downloadFile'])->name('upload.download');


   // Order confirmation/rejection routes
   // Add these routes to the existing routes file within the auth middleware grou

    // Order confirmation/rejection rou
    Route::get('/writer/order/{id}/confirm', [App\Http\Controllers\HomeController::class, 'confirmAssignment'])->name('writer.confirm.assignment');
    Route::get('/writer/order/{id}/reject', [App\Http\Controllers\HomeController::class, 'rejectAssignment'])->name('writer.reject.assignment');
    Route::post('/writer/messages/send', [HomeController::class, 'sendNewMessage'])->name('writer.message.sendNew');
    Route::get('/writer/messages/thread/{orderId}', [HomeController::class, 'viewMessageThread'])->name('writer.message.thread');
    Route::post('/writer/messages/reply', [HomeController::class, 'replyToMessage'])->name('writer.message.reply');
    Route::get('/writer/order/{id}/check-messages', [HomeController::class, 'checkNewMessages'])->name('writer.message.check');
    Route::get('/writer/messages/list', [HomeController::class, 'getMessagesList'])->name('writer.messages.list');
    Route::get('/writer/messages/search', [HomeController::class, 'searchMessages'])->name('writer.messages.search');

    //Route::get('/finance', [HomeController::class, 'userFinance'])->name('writer.finance');
    Route::post('/finance/request-payment', [HomeController::class, 'requestPayment'])->name('finance.request-payment');
    Route::post('/finance/withdraw', [HomeController::class, 'requestWithdrawal'])->name('writer.finance.withdraw');
    Route::get('/finance/order/{id}', [HomeController::class, 'getOrderTransactions'])->name('writer.finance.order');
    Route::post('/finance/filter', [HomeController::class, 'filterFinanceTransactions'])->name('writer.finance.filter');
    
    Route::post('/profile/update', [HomeController::class, 'ProfileUpdate'])->name('profile.update');
    Route::post('/profile/update-status', [HomeController::class, 'updateStatus'])->name('profile.update-status');
    Route::post('/profile/upload-picture', [HomeController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
});