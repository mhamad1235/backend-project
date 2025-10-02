<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use App\Helpers\GlobalHelper;
use Laravel\Sanctum\PersonalAccessToken;
class AccountAuthController extends Controller
{
public function login(Request $request)
{
    
    $request->validate([
        'phone' => 'required',
        'password' => 'required',
    ]);

    $account = Account::where('phone', $request->phone)->first();

    if (! $account || ! Hash::check($request->password, $account->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $accessToken = $account->createToken('access_token_account', ['*'], now()->addMinutes(15))->plainTextToken;

    $refresh_token = $account->createToken('refresh_token_account', ['*'], now()->addDays(30))->plainTextToken;
    return response()->json([
        'account' => $account,
        'access_token' => $accessToken,
        'refresh_token' => $refresh_token,
    ]);
}
 public function refreshToken(Request $request)
{
    $refreshToken = $request->bearerToken();

    $tokenModel = PersonalAccessToken::findToken($refreshToken);

    if (!$tokenModel || $tokenModel->name !== 'refresh_token_account' || $tokenModel->expires_at < now()) {
        return response()->json(['message' => 'Invalid or expired refresh token'], 401);
    }

    $account = $tokenModel->tokenable;
    $account->tokens()->delete();


    $newAccessToken = $account->createToken('access_token_account', ['*'], now()->addMinutes(15))->plainTextToken;
    $newRefreshToken = $account->createToken('refresh_token_account', ['*'], now()->addDays(30))->plainTextToken;

    $data=[
        'access_token' => $newAccessToken,
        'refresh_token' => $newRefreshToken
    ];
    return $this->jsonResponse(data:$data,message:__('successfull'));
}

}
