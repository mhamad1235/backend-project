<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use App\Helpers\GlobalHelper;
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

    $token = $account->createToken('account-token')->plainTextToken;

    return response()->json([
        'account' => $account,
        'token' => $token,
    ]);
}


}
