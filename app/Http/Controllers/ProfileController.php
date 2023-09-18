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
        return view('profile');
    }

    public function update(Request $request) {

        $request->validate([
            'name' =>'required|min:4|string|max:255',
            'email'=>'required|email|string|max:255'
        ]);

        $user = $request->user();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if ($request['role'] != null) {
            $user->role_preference = $request['role'];
        }
        $user->save();
        // return back()->with('message','Profile Updated');
        return view('profile');
    }
}
