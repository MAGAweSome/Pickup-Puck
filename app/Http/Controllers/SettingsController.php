<?php

namespace App\Http\Controllers;

use Rawilk\Settings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings form with saved defaults.
     */
    public function index()
    {
        $defaults = [
            'default_role_preference' => Settings::get('default_role_preference', auth()->user()->role_preference ?? null),
            'default_game_role' => Settings::get('default_game_role', null),
        ];

        return view('settings.index', ['defaults' => $defaults]);
    }

    /**
     * Save settings defaults.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'default_role_preference' => 'nullable|string|max:255',
            'default_game_role' => 'nullable|string|max:255',
        ]);

        Settings::set('default_role_preference', $data['default_role_preference'] ?? null);
        Settings::set('default_game_role', $data['default_game_role'] ?? null);

        return Redirect::route('settings.index')->with('success', 'Settings saved');
    }
}
