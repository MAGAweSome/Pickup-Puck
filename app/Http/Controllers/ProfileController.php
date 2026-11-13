<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        $user = Auth::user();
        $managedProfiles = $user->canManageOthers() ? $user->managedProfiles()->get() : collect();
        $availableGuests = \App\Models\Guest::orderBy('name')->get();

        return view('profile', [
            'managedProfiles' => $managedProfiles,
            'availableGuests' => $availableGuests,
        ]);
    }

    public function update(Request $request) {

        $request->validate([
            'name' =>'required|min:4|string|max:255',
            'email'=>'required|email|string|max:255',
            'jersey_number' => 'nullable|integer|min:0|max:99',
        ]);

        $user = $request->user();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if ($request['role'] != null) {
            $user->role_preference = $request['role'];
        }
        if ($request->has('jersey_number')) {
            $user->jersey_number = $request->input('jersey_number') !== '' ? (int) $request->input('jersey_number') : null;
        }
        $user->save();
        return back()->with('message', 'Profile updated successfully.');
    }
}
