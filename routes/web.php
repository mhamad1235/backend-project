<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FileUploadController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\BookingController;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Admin\CabinController;
Route::get('/', function () {
    return redirect('login');
});

Auth::routes();
//Language Translation


Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'root'])->name(('root'));
    Route::resource("users", UserController::class);
    Route::resource("cities", CityController::class);
    Route::get('/upload', [FileUploadController::class, 'index'])->name('images.index');
    Route::get('/upload/data', [FileUploadController::class, 'data'])->name('images.data');
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('upload.submit');
    Route::get('/images/upload', [FileUploadController::class, 'save'])->name('images.upload');
    Route::post('/images/upload', [FileUploadController::class, 'save'])->name('images.upload');
    Route::post('/images/insert', [FileUploadController::class, 'insert'])->name('images.insert');
    Route::resource('accounts', AccountController::class);
    Route::resource('buses', BusController::class);
    Route::resource('bookings', BookingController::class);
    Route::patch('/bookings/{booking}/update-status', [BookingController::class, 'updateStatus'])
        ->name('bookings.update-status');
    Route::resource('cabins', CabinController::class);
    Route::delete('/images/{image}/delete', [CabinController::class, 'deleteImage'])
        ->name('images.delete');


});
Route::get('/test-websocket', function() {
    return view('websocket-test');
});

Route::get('/trigger', function () {
    // Add logging to verify event dispatch
    \Log::info('Dispatching ActionExecuted event');
    
    // Dispatch synchronously to ensure immediate broadcast
    broadcast(new NewNotificationEvent("Data changed at: ".now()));
    
    return "Event fired! Check logs and Reverb server output.";
});