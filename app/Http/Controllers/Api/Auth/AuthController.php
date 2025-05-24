<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'phone'=>'requiresd',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            );


            Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

            return response()->json([
                'message' => 'User registered. Please check your email for verification.',
                'verification_url' => $verificationUrl
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('phone', $validated["phone"])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->jsonResponse(false, "Wrong password or phone", Response::HTTP_UNAUTHORIZED);
        }

        $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

        $refreshToken = $user->createToken('refresh_token', ['*'], now()->addDays(30))->plainTextToken;

        return $this->jsonResponse(true, "Login successful", Response::HTTP_OK, [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => new UserResource($user->load('city'))
        ]);
    }


}

