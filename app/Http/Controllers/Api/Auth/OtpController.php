<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

            $code = rand(100000, 999999); // 6-digit OTP

            Otp::create([
                'phone' => $formattedPhone,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(5)
            ]);
            
               // Send OTP via otpiq.com API
         $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.otpiq.key'),
                'Content-Type'  => 'application/json',
            ])->post(config('services.otpiq.url'), [
                'phoneNumber'       => '964' . $rawPhone, 
                'smsType'           => 'verification',
                'provider'          => 'whatsapp',
                'verificationCode'  => (string) $code
            ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to send SMS',
                'details' => $response->body()
            ], $response->status());
        }
        // $data=[
        //     'otp'=>$code
        // ];

            return $this->jsonResponse(message:__('OTP sent successfully'));
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

        $token = $user->createToken('auth_token')->plainTextToken;
        $tokenModel = $user->tokens()->latest()->first();
        $tokenModel->expires_at = now()->addMinutes(15);
        $tokenModel->save();
        $data=[
            'token' => $token,
            'verify' => true,
            'is_exist' => true,
            'user' => $user
        ];
        return $this->jsonResponse(data:$data,message:__('Logged in successfully'));

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
        'fcm'=>'required|string'
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
        'fcm'=>$request->fcm
    ]);

    Cache::forget("verify_token_{$request->verify_token}");

    $token = $user->createToken('auth_token')->plainTextToken;
    $tokenModel = $user->tokens()->latest()->first();
    $tokenModel->expires_at = now()->addMinutes(15);
    $tokenModel->save();
    
    $data=[
        'token' => $token,
        'user' => $user
    ];
    return $this->jsonResponse(data:$data,message:__('User registered successfully'));
} catch (\Throwable $th) {
    return $this->jsonResponse(message:__('Technical Problem'),code:500);
}
}

public function refresh(Request $request)
{
    try{
    $user = Auth::user();
    $request->user()->tokens()->delete();
    $tokens = $this->generateTokens($user);

    $data=[
        'token'=>$tokens
    ];

     return $this->jsonResponse(data:$data,message:"Succesful");
}catch(\Throwable $th){
    return $this->jsonResponse(message:__('Technical Problem'),code:500);
}
}

public function generateTokens($user)
{
    $accessTokenExpireAt = now()->addMinutes(15);
    $refreshTokenExpireAt = now()->addDays(60); // 60 days

    // Create access token
    $accessTokenInstance = $user->createToken('auth_token');
    $accessToken = $accessTokenInstance->plainTextToken;

    // Set custom expiration
    $user->tokens()
        ->where('id', $accessTokenInstance->accessToken->id)
        ->update(['expires_at' => $accessTokenExpireAt]);

    // Create refresh token
    $refreshTokenInstance = $user->createToken('refresh_token');
    $refreshToken = $refreshTokenInstance->plainTextToken;

    // Set custom expiration
    $user->tokens()
        ->where('id', $refreshTokenInstance->accessToken->id)
        ->update(['expires_at' => $refreshTokenExpireAt]);

    return [
        'accessToken' => $accessToken,
        'refreshToken' => $refreshToken,
    ];
}

}
