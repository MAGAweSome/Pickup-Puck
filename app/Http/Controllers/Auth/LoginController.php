<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * After successful login, send regular users into onboarding until completed.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->intended($this->redirectPath());
        }

        if (!($user->completed_onboarding ?? false)) {
            return redirect()->route('home', ['onboarding' => 1]);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Override failed login response to provide a clearer message when the
     * submitted email does not exist in our system.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $username = $this->username();

        $user = User::where($username, $request->input($username))->first();

        if (! $user) {
            // Flash the form indicator so the login error only shows on the login tab
            $request->session()->flashInput(array_merge($request->only($username), ['form' => 'login']));

            throw ValidationException::withMessages([
                $username => ['This account has not yet been created.']
            ]);
        }

        // Flash form indicator for failed login attempt
        $request->session()->flashInput(array_merge($request->only($username), ['form' => 'login']));

        throw ValidationException::withMessages([
            $username => [trans('auth.failed')]
        ]);
    }
}
