<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\TwilioController;
use App\Http\Controllers\Auth\Api\LoginController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Models\User;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Api\Auth\OtpController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\DataResourceController;

Route::group(["prefix" => "auth"], function () {
    // Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
    // Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
    Route::post('/register-after-verification', [OtpController::class, 'registerAfterVerification']);
    // Route::post('/send', [TwilioController::class, 'sendOtp']);
    // Route::post('/verify', [TwilioController::class, 'verify']);
});
Route::controller(DataResourceController::class)->group(function () {
    Route::get('cities',  'cities');

});

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('verification.send');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
    ->name('verification.verify');


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('refresh-token', [OtpController::class, 'refreshToken'])
        ->name('refresh');

        Route::get('/test', function(){
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Not authenticated'], 401);
            }
        
            return response()->json([
                'user' => $user->name,
                'city_name' => $user->city->name, // this uses the translated name if using Translatable
            ]);
        });
    });
