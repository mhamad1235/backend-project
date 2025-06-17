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
use App\Http\Controllers\Api\Auth\AccountAuthController;
use Illuminate\Support\Facades\Gate;
use App\Models\Hotel;
use App\Http\Middleware\EnsureAccountRole;
use App\Http\Controllers\Api\Auth\FIBPaymentController;


Route::group(["prefix" => "auth"], function () {
    // Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
    // Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
    // Route::post('/send', [TwilioController::class, 'sendOtp']);
    // Route::post('/verify', [TwilioController::class, 'verify']);


    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
    Route::post('/register-after-verification', [OtpController::class, 'registerAfterVerification']);
    Route::post('/make-payment', [FIBPaymentController::class, 'createPayment']);
    Route::post('/callback', [FIBPaymentController::class, 'handleCallback']);


});

Route::controller(DataResourceController::class)->group(function () {
    Route::get('cities',  'cities');
});

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('verification.send');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
    ->name('verification.verify');

 Route::prefix('account')->group(function () {
    Route::post('/login', [AccountAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', function (Request $request) {
            return $request->user(); // Will return the authenticated Account
        });
    });
});

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/places', [OtpController::class, 'index']);

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
Route::middleware(['auth:account', 'role:motel'])->group(function () {
    Route::get('/motel/dashboard', function () {
        return response()->json([
            'message' => 'Welcome to the Motel dashboard!',
        ]);
    });
});
