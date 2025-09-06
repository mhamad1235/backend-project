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
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\HotelBookingController; 

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



Route::group(["prefix" => "guest"], function () {
Route::get('/restaurants'         ,    [HomeController::class, 'getRestaurants' ]);
Route::get('/restaurants/{id}'    ,    [HomeController::class, 'getRestaurant'  ]);
Route::get('/environments/{type}' ,    [HomeController::class, 'getEnvironments']);
Route::get('/environment/{id}'    ,    [HomeController::class, 'getEnvironment' ]);
Route::get('/journey'             ,    [HomeController::class, 'getJourney'     ]);
Route::get('/journey/{id}'        ,    [HomeController::class, 'getJourneyById'  ]);
Route::get('/hotels'              ,    [HomeController::class, 'getHotels'      ]);
Route::get('/hotel/{id}'          ,    [HomeController::class, 'getHotelById'   ]);

});

Route::controller(DataResourceController::class)->group(function () {
    Route::get('cities',  'cities');
});
Route::controller(DataResourceController::class)->group(function () {
    Route::get('properties',  'properties');
});
Route::controller(DataResourceController::class)->group(function () {
    Route::get('foodtypes',  'foodtypes');
});

Route::controller(DataResourceController::class)->group(function () {
    Route::get('roomtypes',  'roomtypes');
});




// Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
//     ->name('verification.send');

// Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
//     ->name('verification.verify');


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
        
         Route::post('fib/third/{paymentId}', [BookingController::class, 'third']);

         Route::post('join/journey/{id}', [BookingController::class, 'second']);
         Route::post('reject/journey/{paymentId}/{journeyId}', [BookingController::class, 'rejectJourney']);
        
         Route::post('fib/payment-complete', [BookingController::class, 'callback']);

         Route::post('/favorites/{type}/{id}', [FavoriteController::class, 'toggle']);
         Route::get('/favorites', [FavoriteController::class, 'index']);

         Route::post('/book/hotel/{hotel_id}/{room_id}', [HotelBookingController::class, 'bookHotel']);
         Route::post('/fib/hotel/{hotel_id}/{room_id}',  [HotelBookingController::class, 'bookHotelPayment']);
    });
    Route::get('token',function(){
         $client = new \GuzzleHttp\Client();

    // Step 1: Get access token
    $tokenResponse = $client->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
        'form_params' => [
            'grant_type' => 'client_credentials',
            'client_id' => 'mp-it',
            'client_secret' => '2d9d9e4b-8b29-4d74-a393-9b9684975512',
        ],
    ]);

     return $accessToken = json_decode($tokenResponse->getBody(), true)['access_token'];
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
    Route::post('/restaurant/food/{id}',    [RestaurantController::class, 'updateFood']);
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


/////////// Hotel Routes
Route::middleware(['auth:account', 'role:hotel'])->group(function () {
    Route::get('/hotel'                   ,        [HotelController::class, 'getHotel']);
    Route::post('/hotel/create-type'      ,        [HotelController::class, 'createTypeRoom']);
    Route::get('/hotel/room'              ,        [HotelController::class, 'getHotelRoom']);
    Route::post('/hotel/create-room'      ,        [HotelController::class, 'createHotelRoom']);
    Route::post('/hotel/room/create-unit' ,        [HotelController::class, 'createUnitRoom']);
    Route::post('/hotel/unit/make-unavailable' ,   [HotelController::class, 'unitUnavailable']);
    Route::get('/hotel/unit/get-unavailable/{id}' ,[HotelController::class, 'getUnitUnavailable']);
});




   Route::post('callback', [BookingController::class, 'callback']);
   Route::post('callback/hotel', [HotelBookingController::class, 'callbackHotel']);
   Route::post('fib/refund/{paymentId}', [BookingController::class, 'refund']);
   Route::post('fib/payout', [BookingController::class, 'payout']);
   Route::post('/fib/payoutment', [BookingController::class, 'create']);
   Route::post('/fib/authorize/{payoutId}', [BookingController::class, 'authorizePayout']);
   Route::post('/fib/return/{paymentId}', [BookingController::class, 'processPaymentAndAutoPayout']);
  