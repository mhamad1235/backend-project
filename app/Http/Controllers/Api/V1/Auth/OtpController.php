<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Place;
class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        try {
            setAppLocale($request);
            $request->validate([
                'phone' => 'required|digits:10|regex:/^[1-9][0-9]{9}$/'
            ]);

            // Format phone number to +964
            $rawPhone = $request->phone;
            $formattedPhone = '+964' . $rawPhone;

            $code = rand(100000, 999999);
            // 6-digit OTP

            Otp::create([
                'phone' => $formattedPhone,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(5)
            ]);

               // Send OTP via otpiq.com API
        //  $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . config('services.otpiq.key'),
        //         'Content-Type'  => 'application/json',
        //     ])->post(config('services.otpiq.url'), [
        //         'phoneNumber'       => '964' . $rawPhone,
        //         'smsType'           => 'verification',
        //         'provider'          => 'whatsapp',
        //         'verificationCode'  => (string) $code
        //     ]);

        // if (!$response->successful()) {
        //     return response()->json([
        //         'error' => 'Failed to send SMS',
        //         'details' => $response->body()
        //     ], $response->status());
        // }
        $data=[
            'otp'=>$code
        ];

            return $this->jsonResponse(data:$data,message:__('OTP sent successfully'));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


public function verifyOtp(Request $request)
{
    try {
        $request->validate([
            'phone' => 'required|digits:10|regex:/^[1-9][0-9]{9}$/',
            'code' => 'required|string'
        ]);
        $rawPhone = $request->phone;
        $formattedPhone = '+964' . $rawPhone;
        $otp = Otp::where('phone', $formattedPhone)
                  ->where('code', $request->code)
                  ->where('expires_at', '>=', now())
                  ->latest()
                  ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $otp->delete();

        $user = User::where('phone', $formattedPhone)->first();

        if (!$user) {
            // Generate temporary verification token (UUID or random string)
            $token = Str::uuid()->toString();

            // Store in cache with expiry (e.g., 10 minutes)
            Cache::put("verify_token_{$token}", $formattedPhone, now()->addMinutes(10));

              $data=[
                'verify' => true,
                'is_exist' => false,
                'verify_token' => $token];

            return $this->jsonResponse(data:$data,message:__('OTP verified. User not registered yet'));
        }

        //$accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

        //$refreshToken = $user->createToken('refresh_token', ['*'], now()->addDays(30))->plainTextToken;

        //$data=[
         //   'access_token' => $accessToken,
         //   'refresh_token'=>$refreshToken,
         //   'verify' => true,
         //   'is_exist' => true,
        //    'user' => $user
        //];
       // return $this->jsonResponse(data:$data,message:__('Logged in successfully'));

    } catch (\Throwable $th) {
        return $this->jsonResponse(message:__('Technical Problem'),code:500);
    }
}

public function registerAfterVerification(Request $request)
{
    try {
    $request->validate([
        'name' => 'required|string|max:255',
        'dob' => 'required|date',
        'verify_token' => 'required|string',
        'fcm'=>'required|string',
        'password'=>'required|string|max:255',
        'city_id' => 'required|exists:cities,id'
    ]);
    $phone = Cache::get("verify_token_{$request->verify_token}");

    if (!$phone) {
        return $this->jsonResponse(result:false,message: __('Invalid or expired verification token'), code: 401);
    }

    if (User::where('phone', $phone)->exists()) {
        return $this->jsonResponse(result:false,message: __('User already registered'), code: 400);
    }

    $user = User::create([
        'phone' => $phone,
        'name' => $request->name,
        'dob' => $request->dob,
        'fcm'=>$request->fcm,
        'password'=>Hash::make($request->password),
        'city_id'=>$request->city_id
    ]);

    Cache::forget("verify_token_{$request->verify_token}");

    $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

    $refresh_token = $user->createToken('refresh_token', ['*'], now()->addDays(30))->plainTextToken;


    $data=[
        'access_token' => $accessToken,
        'refresh_token'=> $refresh_token,
        'user' => $user
    ];
    return $this->jsonResponse(data:$data,message:__('User registered successfully'));
} catch (\Throwable $th) {
    return $this->jsonResponse(message:__($th->getMessage()),code:500);
}
}

public function refreshToken(Request $request)
{
    $refreshToken = $request->bearerToken();

    $tokenModel = PersonalAccessToken::findToken($refreshToken);

    if (!$tokenModel || $tokenModel->name !== 'refresh_token' || $tokenModel->expires_at < now()) {
        return response()->json(['message' => 'Invalid or expired refresh token'], 401);
    }

    $user = $tokenModel->tokenable;

    // ✅ Revoke all existing tokens (both access and refresh)
    $user->tokens()->delete();

    // ✅ Create new access and refresh tokens
    $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;
    $newRefreshToken = $user->createToken('refresh_token', ['*'], now()->addDays(30))->plainTextToken;

    $data=[
        'access_token' => $newAccessToken,
        'refresh_token' => $newRefreshToken
    ];
    return $this->jsonResponse(data:$data,message:__('successfull'));
}

public function generateTokens($user)
{
    $accessTokenExpireAt = now()->addMinutes(15);
    $refreshTokenExpireAt = now()->addDays(60); 

    
    $accessTokenInstance = $user->createToken('auth_token');
    $accessToken = $accessTokenInstance->plainTextToken;

    
    $user->tokens()
        ->where('id', $accessTokenInstance->accessToken->id)
        ->update(['expires_at' => $accessTokenExpireAt]);

    
    $refreshTokenInstance = $user->createToken('refresh_token');
    $refreshToken = $refreshTokenInstance->plainTextToken;

    
    $user->tokens()
        ->where('id', $refreshTokenInstance->accessToken->id)
        ->update(['expires_at' => $refreshTokenExpireAt]);

    return [
        'accessToken' => $accessToken,
        'refreshToken' => $refreshToken,
    ];
}
  public function index(Request $request)
    {
     setAppLocale($request);;
 $lang = app()->getLocale();
        $places = Place::with('translations')->get()->map(function ($place) use ($lang) {
            return [
                'id' => $place->id,
                'name' => $place->translate($lang)->name ?? '',
                'description' => $place->translate($lang)->description ?? '',
                'image' => asset($place->image),
                'latitude' => $place->latitude,
                'longitude' => $place->longitude,
            ];
        });

        return response()->json([
            'lang' => $lang,
            'data' => $places,
        ]);
    }
}
