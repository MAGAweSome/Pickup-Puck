<?php

namespace App\Http\Controllers;

use App\Models\Games\AfterGameEvent;
use App\Models\Games\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfterGameEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new after-game hangout event and automatically sign up the creator.
     */
    public function store(Request $request, Game $game)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'title' => 'nullable|string|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $event = AfterGameEvent::create([
            'game_id' => $game->id,
            'creator_id' => $user->id,
            'location' => trim($validated['location']),
            'title' => !empty($validated['title']) ? trim($validated['title']) : null,
            'description' => !empty($validated['description']) ? trim($validated['description']) : null,
        ]);

        // Automatically sign up the creator
        $event->attendees()->syncWithoutDetaching([
            $user->id => ['note' => 'Organizer']
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'After-game event created at ' . $event->location . '! You are automatically signed up.',
                'event_id' => $event->id,
            ]);
        }

        return back()->with('success', 'After-game event created at ' . $event->location . '! You are automatically signed up.');
    }

    /**
     * Join an after-game event.
     */
    public function join(Request $request, Game $game, AfterGameEvent $event)
    {
        if ($event->game_id !== $game->id) {
            abort(404);
        }

        $userId = Auth::id();

        // Optional target user (if manager is signing up a connected profile)
        $targetUserId = $request->input('user_id');
        if ($targetUserId && (int)$targetUserId !== $userId) {
            $user = Auth::user();
            if (!$user->managedProfiles()->where('id', $targetUserId)->exists()) {
                abort(403, 'Unauthorized to sign up this profile.');
            }
            $userId = (int)$targetUserId;
        }

        $event->attendees()->syncWithoutDetaching([$userId]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Signed up for ' . $event->location . ' hangout!',
            ]);
        }

        return back()->with('success', 'You are signed up for ' . $event->location . ' hangout!');
    }

    /**
     * Leave an after-game event.
     */
    public function leave(Request $request, Game $game, AfterGameEvent $event)
    {
        if ($event->game_id !== $game->id) {
            abort(404);
        }

        $userId = Auth::id();

        // Optional target user (if manager is removing a connected profile)
        $targetUserId = $request->input('user_id');
        if ($targetUserId && (int)$targetUserId !== $userId) {
            $user = Auth::user();
            if (!$user->managedProfiles()->where('id', $targetUserId)->exists()) {
                abort(403, 'Unauthorized to modify this profile.');
            }
            $userId = (int)$targetUserId;
        }

        $event->attendees()->detach($userId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You left the ' . $event->location . ' event.',
            ]);
        }

        return back()->with('success', 'You have left the ' . $event->location . ' event.');
    }

    /**
     * Delete an after-game event (creator or admin).
     */
    public function destroy(Request $request, Game $game, AfterGameEvent $event)
    {
        if ($event->game_id !== $game->id) {
            abort(404);
        }

        $user = Auth::user();
        if ($event->creator_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized to delete this event.');
        }

        $event->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'After-game event removed.',
            ]);
        }

        return back()->with('success', 'After-game event removed.');
    }
}
