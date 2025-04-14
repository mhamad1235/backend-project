<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
class OtpController extends Controller
{
    public function sendOtp(Request $request)
{
    try{
        $request->validate([
            'phone' => 'required|numeric'
        ]);

        $code = rand(100000, 999999); // 6-digit OTP
        Otp::create([
            'phone' => $request->phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);

        // Send OTP via SMS gateway here
        // For testing:
        return response()->json(['message' => 'OTP sent', 'otp' => $code]);
    }catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

public function verifyOtp(Request $request)
{
    try {
        $request->validate([
            'phone' => 'required|numeric',
            'code' => 'required|string'
        ]);

        $otp = Otp::where('phone', $request->phone)
                  ->where('code', $request->code)
                  ->where('expires_at', '>=', now())
                  ->latest()
                  ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $otp->delete();

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            // Generate temporary verification token (UUID or random string)
            $token = Str::uuid()->toString();

            // Store in cache with expiry (e.g., 10 minutes)
            Cache::put("verify_token_{$token}", $request->phone, now()->addMinutes(10));

            return response()->json([
                'message' => 'OTP verified. User not registered yet.',
                'verify' => true,
                'is_exist' => false,
                'verify_token' => $token,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'verify' => true,
            'is_exist' => true,
            'user' => $user
        ]);

    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

public function registerAfterVerification(Request $request)
{
    try {
        //code...

    $request->validate([
        'name' => 'required|string|max:255',
        'birth_date' => 'required|date',
        'verify_token' => 'required|string',
    ]);

    // Get phone from token
    $phone = Cache::get("verify_token_{$request->verify_token}");

    if (!$phone) {
        return response()->json(['message' => 'Invalid or expired verification token'], 401);
    }

    // Optionally check again if user already exists
    if (User::where('phone', $phone)->exists()) {
        return response()->json(['message' => 'User already registered'], 400);
    }

    // Create user
    $user = User::create([
        'phone' => $phone,
        'name' => $request->name,
        'birth_date' => $request->birth_date,
        // add password or other fields if needed
    ]);

    // Remove the token from cache
    Cache::forget("verify_token_{$request->verify_token}");

    // Create token (auth)
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully',
        'token' => $token,
        'user' => $user
    ]);
} catch (\Throwable $th) {
    return response()->json(['error' => $th->getMessage()], 500);
}
}

}
