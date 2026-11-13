<?php

namespace App\Services;

use App\Models\Games\Game;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $publicKey = config('services.webpush.public_key');
        $privateKey = config('services.webpush.private_key');
        $subject = config('services.webpush.subject', 'mailto:admin@pickuppuck.ca');

        if ($publicKey && $privateKey) {
            try {
                $this->webPush = new WebPush([
                    'VAPID' => [
                        'subject' => $subject,
                        'publicKey' => $publicKey,
                        'privateKey' => $privateKey,
                    ],
                ]);
                $this->webPush->setReuseVAPIDHeaders(true);
            } catch (\Throwable $e) {
                Log::error('[WebPushService] Failed to initialize WebPush: ' . $e->getMessage());
                $this->webPush = null;
            }
        }
    }

    /**
     * Send a notification to a specific user across all their active device subscriptions.
     */
    public function sendNotificationToUser(User $user, string $title, string $body, ?string $url = null, array $extraData = []): int
    {
        // Respect user notification preference
        if (isset($user->notify_team_assignment) && !$user->notify_team_assignment) {
            Log::info("[WebPushService] User #{$user->id} ({$user->name}) has disabled team assignment notifications.");
            return 0;
        }

        if (!$this->webPush) {
            Log::warning('[WebPushService] WebPush client is not configured.');
            return 0;
        }

        $subscriptions = $user->pushSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => asset('icons/icon-192x192.png'),
            'badge' => asset('icons/favicon-32x32.png'),
            'url' => $url ?? url('/home'),
            'data' => array_merge([
                'url' => $url ?? url('/home'),
                'timestamp' => now()->timestamp,
            ], $extraData),
        ]);

        $sentCount = 0;
        $expiredIds = [];

        foreach ($subscriptions as $sub) {
            try {
                $webPushSub = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?? 'aes128gcm',
                ]);

                $this->webPush->queueNotification($webPushSub, $payload);
            } catch (\Throwable $e) {
                Log::warning("[WebPushService] Invalid subscription #{$sub->id}: " . $e->getMessage());
            }
        }

        try {
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    Log::warning("[WebPushService] Push failed for {$endpoint}: " . $report->getReason());
                    if ($report->isSubscriptionExpired()) {
                        // Mark expired subscriptions for deletion
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('[WebPushService] Flush error: ' . $e->getMessage());
        }

        return $sentCount;
    }

    /**
     * Send team assignment push notification ("You are on (Team) team!").
     * Supports both initial team generation and manual team switches.
     */
    public function sendTeamAssignmentNotification(User $user, Game $game, int $teamNo, bool $isManualSwitch = false): int
    {
        $teamColor = $teamNo === 1 
            ? ($game->team_1_color ?? 'Dark') 
            : ($game->team_2_color ?? 'White');

        $teamColor = ucfirst(strtolower($teamColor));

        if ($isManualSwitch) {
            $title = 'Team Switch • Pickup Puck';
            $body = "You are on {$teamColor} team!";
        } else {
            $title = 'Roster Set • Pickup Puck';
            $body = "You are on {$teamColor} team!";
        }

        $url = url('/game/' . $game->id);

        return $this->sendNotificationToUser($user, $title, $body, $url, [
            'game_id' => $game->id,
            'team' => $teamNo,
            'team_color' => $teamColor,
            'is_switch' => $isManualSwitch,
        ]);
    }
}
