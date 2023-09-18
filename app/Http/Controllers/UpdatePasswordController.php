<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {      
        return view('update_password');
    }

    public function updatePassword(Request $request)
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Validate and update the user's password
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if the provided current password matches the user's actual password
        if (Hash::check($request->current_password, $user->password)) {
            // Update the user's password with the new password
            $user->password = Hash::make($request->password);
            $user->save();

            // Password updated successfully
            return redirect()->back()->with('success', 'Password updated successfully.');
        } else {
            // Current password does not match
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
    }
}
