<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SetupController extends Controller
{
    public function showElevateMePuckAdmin(): View
    {
        return view('setup.elevate');
    }

    public function elevateMePuckAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'setup_key' => ['required', 'string'],
        ]);

        $expectedKey = (string) (config('app.admin_setup_key') ?? env('ADMIN_SETUP_KEY', ''));

        if ($expectedKey === '') {
            return back()
                ->withErrors(['setup_key' => 'Admin setup is not configured.'])
                ->withInput();
        }

        $providedKey = (string) $request->input('setup_key');

        if (!hash_equals($expectedKey, $providedKey)) {
            return back()
                ->withErrors(['setup_key' => 'Invalid setup key.'])
                ->withInput();
        }

        $user = $request->user();

        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        return redirect()->route('home')->with('success', 'You are now an admin.');
    }
}
