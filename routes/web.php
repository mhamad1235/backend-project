<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FileUploadController;
use App\Http\Controllers\Admin\AccountController;
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
    Route::resource('accounts', AccountController::class);


});

