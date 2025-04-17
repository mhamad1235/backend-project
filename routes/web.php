<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\HomeController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
//Language Translation


Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'root'])->name(('root'));
});

