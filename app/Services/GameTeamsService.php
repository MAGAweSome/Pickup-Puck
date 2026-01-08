<?php

namespace App\Services;

use App\Models\Games\Game;
use App\Models\Games\GameTeam;
use App\Models\Games\GameTeamsGuest;
use App\Models\Games\GameTeamsPlayer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GameTeamsService
{
    private function pickTeamByCount(int $team1Count, int $team2Count): int
    {
        if ($team1Count < $team2Count) return 1;
        if ($team2Count < $team1Count) return 2;
        // Tie => truly random team
        return random_int(0, 1) === 0 ? 1 : 2;
    }

    /**
     * Ensures teams are persisted and team assignments are stable.
     *
     * Rules:
     * - Teams only "lock" at T-30.
     * - Once locked, existing assignments are never changed.
     * - New users/guests who join after lock are assigned to a team automatically.
     */
    public function ensureLockedTeams(Game $game, Carbon $now): void
    {
        $revealAt = $game->time->copy()->subMinutes(30);
        if ($now->lessThan($revealAt)) {
            return;
        }

        // Ensure team rows exist for the game.
        foreach ([1, 2] as $teamNo) {
            GameTeam::query()->updateOrCreate(
                ['game_id' => $game->id, 'team' => $teamNo],
                ['locked_at' => $now]
            );
        }

        $goalieIds = $game->goalies->pluck('id')->map(fn ($id) => (int) $id)->values();
        $playerIds = $game->players->pluck('id')->map(fn ($id) => (int) $id)->values();

        $guestGoalieIds = DB::table('game_players_guests')
            ->where('game_id', $game->id)
            ->where('role', 'goalie')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $guestPlayerIds = DB::table('game_players_guests')
            ->where('game_id', $game->id)
            ->where('role', 'player')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $allUserIds = $playerIds->merge($goalieIds)->unique()->values();
        $allGuestIds = $guestPlayerIds->merge($guestGoalieIds)->unique()->values();
        if ($allUserIds->isEmpty() && $allGuestIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($game, $goalieIds, $playerIds, $guestGoalieIds, $guestPlayerIds, $allUserIds, $allGuestIds) {
            $existingUsers = DB::table('game_teams_players')
                ->where('game_id', $game->id)
                ->get(['user_id', 'team']);
            $existingGuests = DB::table('game_teams_guests')
                ->where('game_id', $game->id)
                ->get(['guest_id', 'team']);

            // No existing assignments => initial locked teams (random but goalie-aware).
            if ($existingUsers->isEmpty() && $existingGuests->isEmpty()) {
                $teamUsers = [1 => [], 2 => []];
                $teamGuests = [1 => [], 2 => []];

                $goaliePool = collect();
                foreach ($goalieIds->all() as $id) $goaliePool->push(['type' => 'user', 'id' => (int) $id]);
                foreach ($guestGoalieIds->all() as $id) $goaliePool->push(['type' => 'guest', 'id' => (int) $id]);
                $goaliePool = $goaliePool->shuffle()->values();

                $assignGoalie = function (int $teamNo, array $goalie) use (&$teamUsers, &$teamGuests) {
                    if ($goalie['type'] === 'user') $teamUsers[$teamNo][] = $goalie['id'];
                    else $teamGuests[$teamNo][] = $goalie['id'];
                };

                // Spread goalies across teams when possible.
                if ($goaliePool->count() >= 1) $assignGoalie(1, $goaliePool[0]);
                if ($goaliePool->count() >= 2) $assignGoalie(2, $goaliePool[1]);

                $skaterPool = collect();
                foreach ($playerIds->all() as $id) $skaterPool->push(['type' => 'user', 'id' => (int) $id]);
                foreach ($guestPlayerIds->all() as $id) $skaterPool->push(['type' => 'guest', 'id' => (int) $id]);
                $skaterPool = $skaterPool->shuffle()->values();

                foreach ($skaterPool as $m) {
                    $teamNo = $this->pickTeamByCount(
                        count($teamUsers[1]) + count($teamGuests[1]),
                        count($teamUsers[2]) + count($teamGuests[2])
                    );
                    if ($m['type'] === 'user') $teamUsers[$teamNo][] = $m['id'];
                    else $teamGuests[$teamNo][] = $m['id'];
                }

                foreach ($teamUsers as $teamNo => $ids) {
                    foreach ($ids as $uid) {
                        GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => $uid, 'team' => $teamNo]);
                    }
                }
                foreach ($teamGuests as $teamNo => $ids) {
                    foreach ($ids as $gid) {
                        GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => $gid, 'team' => $teamNo]);
                    }
                }

                return;
            }

            $assignedUserIds = $existingUsers->pluck('user_id')->map(fn ($id) => (int) $id)->unique();
            $assignedGuestIds = $existingGuests->pluck('guest_id')->map(fn ($id) => (int) $id)->unique();
            $missingUserIds = $allUserIds->diff($assignedUserIds)->values();
            $missingGuestIds = $allGuestIds->diff($assignedGuestIds)->values();

            $teamCount = function (int $teamNo) use ($game) {
                $u = (int) DB::table('game_teams_players')->where('game_id', $game->id)->where('team', $teamNo)->count();
                $g = (int) DB::table('game_teams_guests')->where('game_id', $game->id)->where('team', $teamNo)->count();
                return $u + $g;
            };

            // Track whether each team already has a goalie (across both users+guests).
            $teamHasGoalie = [1 => false, 2 => false];
            foreach ($goalieIds->all() as $uid) {
                $row = DB::table('game_teams_players')->where('game_id', $game->id)->where('user_id', (int) $uid)->first();
                if ($row) $teamHasGoalie[(int) $row->team] = true;
            }
            foreach ($guestGoalieIds->all() as $gid) {
                $row = DB::table('game_teams_guests')->where('game_id', $game->id)->where('guest_id', (int) $gid)->first();
                if ($row) $teamHasGoalie[(int) $row->team] = true;
            }

            // Add missing goalies first, preferring any team that doesn't yet have one.
            $missingUserGoalies = $missingUserIds->intersect($goalieIds)->shuffle()->values();
            foreach ($missingUserGoalies as $uid) {
                $targetTeam = !$teamHasGoalie[1]
                    ? 1
                    : (!$teamHasGoalie[2]
                        ? 2
                        : $this->pickTeamByCount($teamCount(1), $teamCount(2))
                    );
                GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => (int) $uid, 'team' => $targetTeam]);
                $teamHasGoalie[$targetTeam] = true;
                $missingUserIds = $missingUserIds->diff([(int) $uid])->values();
            }

            $missingGuestGoalies = $missingGuestIds->intersect($guestGoalieIds)->shuffle()->values();
            foreach ($missingGuestGoalies as $gid) {
                $targetTeam = !$teamHasGoalie[1]
                    ? 1
                    : (!$teamHasGoalie[2]
                        ? 2
                        : $this->pickTeamByCount($teamCount(1), $teamCount(2))
                    );
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
                $teamHasGoalie[$targetTeam] = true;
                $missingGuestIds = $missingGuestIds->diff([(int) $gid])->values();
            }

            // Add remaining missing skaters/attendees to the smaller team.
            foreach ($missingUserIds->shuffle()->values() as $uid) {
                $targetTeam = $this->pickTeamByCount($teamCount(1), $teamCount(2));
                GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => (int) $uid, 'team' => $targetTeam]);
            }

            foreach ($missingGuestIds->shuffle()->values() as $gid) {
                $targetTeam = $this->pickTeamByCount($teamCount(1), $teamCount(2));
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
            }
        });
    }
}
