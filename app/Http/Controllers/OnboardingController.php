<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gameDetailsDemo(Request $request)
    {
        $user = $request->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->route('home');
        }

        return view('onboarding.game_detail_demo');
    }

    public function complete(Request $request)
    {
        $user = $request->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return response()->json(['ok' => false, 'message' => 'Admins do not use this onboarding flow.'], 403);
        }

        $user->completed_onboarding = true;
        $user->save();

        return response()->json(['ok' => true]);
    }
}
