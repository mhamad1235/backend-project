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
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\RestaurantController;


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

        Route::post('refresh-token', [OtpController::class, 'refreshToken'])->name('refresh');
         Route::get('/buses', [BookingController::class, 'getBuses']);
         Route::post('/bookings/buses/{bus}', [BookingController::class, 'createBooking']);
         Route::get('/bookings', [BookingController::class, 'getUserBookings']);
         Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancelBooking']);
            



         Route::post('fib/first', [BookingController::class, 'first']);
         Route::post('join/journey/{id}', [BookingController::class, 'second']);
         Route::post('fib/third/{paymentId}', [BookingController::class, 'third']);
       
         Route::post('fib/refund/{paymentId}', [BookingController::class, 'refund']);
         Route::post('fib/payment-complete', [BookingController::class, 'callback']);
    });



    Route::middleware(['auth:account', 'role:motel'])->group(function () {
    Route::get('/motel/dashboard', function () {
        return response()->json([
            'message' => 'Welcome to the Motel dashboard!',
        ]);
    });
});



Route::post('/test-image', [AccountAuthController::class, 'testImage'])->name('api.test.image');

// Route::get('/test-event', function () {
//  broadcast(new NewNotificationEvent('🚨 New message'));
//     return 'Sent!';
// });
Route::post('/bookings/{booking}', [BookingController::class, 'store']);
        





/////////// Restarant Routes
Route::middleware(['auth:account', 'role:restaurant'])->group(function () {
    Route::get('/restaurants/{id}'      ,   [RestaurantController::class, 'show']);
    Route::post('/restaurants'          ,   [RestaurantController::class, 'store']);
    Route::delete('/restaurants/{id}'   ,   [RestaurantController::class, 'destroy']);
    Route::post('/restaurants/food'     ,   [RestaurantController::class, 'storeFood']);
    Route::delete('/restaurant/food/{id}',  [RestaurantController::class, 'deleteFood']);
    Route::get('/restaurant/food/{id}' ,    [RestaurantController::class, 'showFood']);
    Route::post('/restaurant/food/{id}',   [RestaurantController::class, 'updateFood']);
    Route::get('restaurant/food'        ,   [RestaurantController::class, 'foods']);
});



///////// Journey Routes
Route::middleware(['auth:account', 'role:tourist'])->group(function () {
    Route::get('/journeys', [JourneyController::class, 'index']);
    Route::get('/journeys/{id}', [JourneyController::class, 'show']);
    Route::post('/journeys', [JourneyController::class, 'store']);
    Route::post('/journeys/{id}', [JourneyController::class, 'update']);
    Route::delete('/journeys/{id}', [JourneyController::class, 'destroy']);
});

  Route::post('callback', [BookingController::class, 'callback']);