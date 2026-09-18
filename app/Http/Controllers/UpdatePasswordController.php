<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirect legacy password update route to the modern profile password card.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {      
        return redirect()->route('profile');
    }

    /**
     * Validate and update the authenticated user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Validate current password and new password requirements
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ], [
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'The provided current password does not match our records.'])
                ->withInput();
        }

        // Prevent reusing identical password
        if (Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'Your new password cannot be the same as your current password.'])
                ->withInput();
        }

        // Save new password
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('password_status', 'Your password has been successfully updated!');
    }
}
