<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // Require a first name and a last name (last name at least 2 chars) per requested pattern
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z]+ [A-Za-z]{2,}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.regex' => 'Please enter first and last name'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Normalize the name to Title Case and trim whitespace before saving
        $name = trim($data['name']);
        if (function_exists('mb_convert_case')) {
            $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        } else {
            $name = ucwords(strtolower($name));
        }

        if (DB::table('guests')->where('name', $name)->exists()) {
            DB::table('guests')->where('name', $name)->delete();
        }

        return User::create([
            'name' => $name,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * AJAX: Check whether an email is already registered.
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $exists = User::where('email', $request->input('email'))->exists();

        return response()->json(['exists' => $exists]);
    }
}
