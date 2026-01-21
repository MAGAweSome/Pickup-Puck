<?php

namespace App\Services;

use App\Models\Games\Game;
use App\Models\Games\GameTeam;
use App\Models\Games\GameTeamsGuest;
use App\Models\Games\GameTeamsPlayer;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class GameTeamsService
{
    private function pickTeamByCount(int $team1Count, int $team2Count): int
    {
        if ($team1Count < $team2Count) return 1;
        if ($team2Count < $team1Count) return 2;
        // Tie => truly random team
        return random_int(0, 1) === 0 ? 1 : 2;
    }

    private function pickTeamBySkill(int $team1Count, int $team2Count, int $team1Skill, int $team2Skill): int
    {
        // Prefer the team with fewer members
        if ($team1Count < $team2Count) return 1;
        if ($team2Count < $team1Count) return 2;

        // If counts equal prefer the team with lower skill sum to balance teams
        if ($team1Skill < $team2Skill) return 1;
        if ($team2Skill < $team1Skill) return 2;

        // Still tied => random
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

        // Pre-fetch level maps for users and guests (defaults to 3 when missing)
        $userLevelMap = DB::table('users')
            ->whereIn('id', $allUserIds->all())
            ->pluck('level', 'id')
            ->mapWithKeys(fn($v, $k) => [(int) $k => (int) $v])
            ->all();

        $guestLevelMap = DB::table('game_players_guests')
            ->whereIn('id', $allGuestIds->all())
            ->pluck('level', 'id')
            ->mapWithKeys(fn($v, $k) => [(int) $k => (int) $v])
            ->all();

        DB::transaction(function () use ($game, $goalieIds, $playerIds, $guestGoalieIds, $guestPlayerIds, $allUserIds, $allGuestIds, $userLevelMap, $guestLevelMap) {
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

                // Track team skill sums (levels)
                $teamSkill = [1 => 0, 2 => 0];

                $goaliePool = collect();
                foreach ($goalieIds->all() as $id) $goaliePool->push(['type' => 'user', 'id' => (int) $id, 'level' => $userLevelMap[$id] ?? 3]);
                foreach ($guestGoalieIds->all() as $id) $goaliePool->push(['type' => 'guest', 'id' => (int) $id, 'level' => $guestLevelMap[$id] ?? 3]);
                $goaliePool = $goaliePool->shuffle()->values();

                $assignGoalie = function (int $teamNo, array $goalie) use (&$teamUsers, &$teamGuests, &$teamSkill) {
                    if ($goalie['type'] === 'user') $teamUsers[$teamNo][] = $goalie['id'];
                    else $teamGuests[$teamNo][] = $goalie['id'];
                    $teamSkill[$teamNo] += (int) ($goalie['level'] ?? 3);
                };

                // Spread goalies across teams when possible.
                if ($goaliePool->count() >= 1) $assignGoalie(1, $goaliePool[0]);
                if ($goaliePool->count() >= 2) $assignGoalie(2, $goaliePool[1]);

                $skaterPool = collect();
                foreach ($playerIds->all() as $id) $skaterPool->push(['type' => 'user', 'id' => (int) $id, 'level' => $userLevelMap[$id] ?? 3]);
                foreach ($guestPlayerIds->all() as $id) $skaterPool->push(['type' => 'guest', 'id' => (int) $id, 'level' => $guestLevelMap[$id] ?? 3]);
                $skaterPool = $skaterPool->shuffle()->values();

                foreach ($skaterPool as $m) {
                    $teamNo = $this->pickTeamBySkill(
                        count($teamUsers[1]) + count($teamGuests[1]),
                        count($teamUsers[2]) + count($teamGuests[2]),
                        $teamSkill[1],
                        $teamSkill[2]
                    );
                    if ($m['type'] === 'user') $teamUsers[$teamNo][] = $m['id'];
                    else $teamGuests[$teamNo][] = $m['id'];
                    $teamSkill[$teamNo] += (int) ($m['level'] ?? 3);
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

            // Compute current team skill sums from existing assignments
            $teamSkill = [1 => 0, 2 => 0];
            foreach ($existingUsers as $row) {
                $lvl = $userLevelMap[(int) $row->user_id] ?? 3;
                $teamSkill[(int) $row->team] = ($teamSkill[(int) $row->team] ?? 0) + (int) $lvl;
            }
            foreach ($existingGuests as $row) {
                $lvl = $guestLevelMap[(int) $row->guest_id] ?? 3;
                $teamSkill[(int) $row->team] = ($teamSkill[(int) $row->team] ?? 0) + (int) $lvl;
            }

            // Add missing goalies first, preferring any team that doesn't yet have one.
            $missingUserGoalies = $missingUserIds->intersect($goalieIds)->shuffle()->values();
            foreach ($missingUserGoalies as $uid) {
                $level = $userLevelMap[$uid] ?? 3;
                $targetTeam = !$teamHasGoalie[1]
                    ? 1
                    : (!$teamHasGoalie[2]
                        ? 2
                        : $this->pickTeamBySkill($teamCount(1), $teamCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0)
                    );
                GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => (int) $uid, 'team' => $targetTeam]);
                $teamHasGoalie[$targetTeam] = true;
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
                $missingUserIds = $missingUserIds->diff([(int) $uid])->values();
            }

            $missingGuestGoalies = $missingGuestIds->intersect($guestGoalieIds)->shuffle()->values();
            foreach ($missingGuestGoalies as $gid) {
                $level = $guestLevelMap[$gid] ?? 3;
                $targetTeam = !$teamHasGoalie[1]
                    ? 1
                    : (!$teamHasGoalie[2]
                        ? 2
                        : $this->pickTeamBySkill($teamCount(1), $teamCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0)
                    );
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
                $teamHasGoalie[$targetTeam] = true;
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
                $missingGuestIds = $missingGuestIds->diff([(int) $gid])->values();
            }

            // Add remaining missing skaters/attendees to the smaller/less-skilled team.
            foreach ($missingUserIds->shuffle()->values() as $uid) {
                $level = $userLevelMap[$uid] ?? 3;
                $targetTeam = $this->pickTeamBySkill($teamCount(1), $teamCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0);
                GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => (int) $uid, 'team' => $targetTeam]);
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
            }

            foreach ($missingGuestIds->shuffle()->values() as $gid) {
                $level = $guestLevelMap[$gid] ?? 3;
                $targetTeam = $this->pickTeamBySkill($teamCount(1), $teamCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0);
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
            }
        });
    }
}
