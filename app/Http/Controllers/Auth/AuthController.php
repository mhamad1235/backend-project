<?php

namespace App\Http\Controllers\Auth;
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
class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
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

        if ($validated["credential_type"] == 'email') {
            $user = User::where('email', $validated["email_or_phone"])->first();
        } else {
            $user = User::where('phone', $validated["email_or_phone"])->first();
        }

        //check password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->jsonResponse(false,"Wrong password or email", Response::HTTP_UNAUTHORIZED);
        }

        //create token and attach to user
        $token =  $user->createToken('auth_token')->plainTextToken;
        $user["user_token"] = $token;

        return $this->jsonResponse(true,"successful", Response::HTTP_OK, $user);
    }

}

