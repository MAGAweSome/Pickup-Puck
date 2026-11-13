<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the VAPID Public Key for Web Push subscription.
     */
    public function getPublicKey(): JsonResponse
    {
        $publicKey = config('services.webpush.public_key');

        return response()->json([
            'publicKey' => $publicKey,
        ]);
    }

    /**
     * Store or update a device push subscription.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'nullable|string',
            'keys.auth' => 'nullable|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $user = Auth::user();

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => $user->id,
                'public_key' => $data['keys']['p256dh'] ?? null,
                'auth_token' => $data['keys']['auth'] ?? null,
                'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm',
            ]
        );

        // Ensure user has notifications enabled when subscribing
        if (!$user->notify_team_assignment) {
            $user->notify_team_assignment = true;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Push subscription saved successfully.',
        ]);
    }

    /**
     * Unsubscribe a device endpoint.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $data['endpoint'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed.',
        ]);
    }

    /**
     * Toggle or update user's notification preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'notify_team_assignment' => 'required|boolean',
        ]);

        $user = Auth::user();
        $user->notify_team_assignment = $data['notify_team_assignment'];
        $user->save();

        return response()->json([
            'success' => true,
            'notify_team_assignment' => (bool) $user->notify_team_assignment,
            'message' => $user->notify_team_assignment 
                ? 'Team assignment notifications enabled.' 
                : 'Team assignment notifications muted.',
        ]);
    }

    /**
     * Send a test push notification to verify the device can receive notifications.
     */
    public function sendTestNotification(): JsonResponse
    {
        $user = Auth::user();
        $webPushService = new WebPushService();

        $sentCount = $webPushService->sendNotificationToUser(
            $user,
            'Pickup Puck • Test Alert',
            'You are on Dark team! Notifications are working perfectly on this device.',
            url('/profile')
        );

        if ($sentCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "Test notification sent to {$sentCount} device(s)!",
            ]);
        }

        $hasSubs = $user->pushSubscriptions()->exists();
        if (!$hasSubs) {
            return response()->json([
                'success' => false,
                'message' => 'No active push subscription found for this device. Please toggle notifications ON.',
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => 'Push notification was queued but could not be delivered. Check device notification permissions.',
        ], 500);
    }
}
