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
use App\Http\Controllers\Admin\EnvironmentController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\FeedbackController;

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();
//Language Translation


Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'root'])->name(('root'));
    Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
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
    Route::resource('environments', EnvironmentController::class);
    Route::delete('/admin/images/{image}/delete', [EnvironmentController::class, 'deleteImage'])->name('images.delete');
    Route::delete('/images/{image}/delete', [EnvironmentController::class, 'deleteImage'])
        ->name('images.delete');

     
Route::resource('restaurants', RestaurantController::class);

Route::resource('restaurants.foods', FoodController::class);

Route::prefix('restaurants/{restaurant}/foods')->group(function () {
    Route::get('/', [FoodController::class, 'index'])->name('restaurants.foods.index');
    Route::get('/create', [FoodController::class, 'create'])->name('restaurants.foods.create');
    Route::post('/', [FoodController::class, 'store'])->name('restaurants.foods.store');
    Route::get('/{food}/edit', [FoodController::class, 'foodsEdit'])->name('restaurants.foods.edit');
    Route::put('/{food}', [FoodController::class, 'foodsUpdate'])->name('restaurants.foods.update');
    Route::delete('/{food}', [FoodController::class, 'foodsDestroy'])->name('restaurants.foods.destroy');
});
 
  Route::resource('hotels', HotelController::class);
  Route::get('hotels/{hotel}/detail', [HotelController::class, 'detail'])->name('hotels.detail');
  Route::get('hotels/{hotel}/{room}/unit', [HotelController::class, 'unit'])->name('hotels.unit');
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks.index');
    Route::put('/feedbacks/{feedback}/status', [FeedbackController::class, 'updateStatus'])->name('admin.feedbacks.update-status');
        Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('admin.feedbacks.destroy');
});
 Route::delete('/admin/images/hotel/{image}/delete', [HotelController::class, 'deleteImage'])->name('hotels.deleteImage');
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

    Route::get('/test', [HomeController::class, 'test'])->name(('test'));

    // Environment Slots Management
Route::prefix('environments/{environment}/slots')->group(function () {
    Route::get('/', [EnvironmentController::class, 'slotsIndex'])->name('environments.slots.index');
    Route::post('/', [EnvironmentController::class, 'storeSlot'])->name('environments.slots.store');
    Route::put('/{slot}', [EnvironmentController::class, 'updateSlot'])->name('environments.slots.update');
    Route::delete('/{slot}', [EnvironmentController::class, 'destroySlot'])->name('environments.slots.destroy');
});