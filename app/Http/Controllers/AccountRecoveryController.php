<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountRecoveryController extends Controller
{
    /**
     * Validate that the user has an active, unexpired single-use token matching the query nonce.
     */
    protected function hasValidSingleUseToken(User $user, Request $request): bool
    {
        $provided = (string) $request->query('token', $request->input('token', ''));

        if (
            $provided === '' ||
            empty($user->account_recovery_token) ||
            empty($user->account_recovery_expires_at)
        ) {
            return false;
        }

        if (now()->greaterThan($user->account_recovery_expires_at)) {
            return false;
        }

        return hash_equals($user->account_recovery_token, hash('sha256', $provided));
    }

    /**
     * Render the public self-service profile & password update page.
     */
    public function show(Request $request, User $user)
    {
        if (!$this->hasValidSingleUseToken($user, $request)) {
            return response()->view('auth.account-recovery-expired', [], 403);
        }

        return view('auth.account-recovery', [
            'user' => $user,
            'token' => (string) $request->query('token', ''),
            'actionUrl' => $request->fullUrl(),
            'expiresAt' => $user->account_recovery_expires_at,
        ]);
    }

    /**
     * Persist updated name, email, and hashed password, then invalidate the single-use token.
     */
    public function update(Request $request, User $user)
    {
        if (!$this->hasValidSingleUseToken($user, $request)) {
            return response()->view('auth.account-recovery-expired', [], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        // Single-use invalidation immediately upon save
        $user->account_recovery_token = null;
        $user->account_recovery_expires_at = null;
        $user->save();

        return redirect()
            ->route('login')
            ->with('status', 'Your account details and password have been updated! You can now sign in with your new credentials.');
    }
}
