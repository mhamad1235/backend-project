<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $user = Admin::where($this->username(), $request->{$this->username()})->first();

        if (!$user || Hash::check($request->password, $user->password) == false) {
            $message = 'auth.failed';
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            throw ValidationException::withMessages([
                $this->username() => [trans($message)],
            ]);
        }

    

        auth('admin')->login($user);

        return redirect()->intended($this->redirectTo);
    }

    public function username()
    {
        return 'email'; // Customize as needed (e.g., 'username' or other field)
    }
}
