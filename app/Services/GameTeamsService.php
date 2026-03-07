<?php

namespace App\Services;

use App\Models\Games\Game;
use App\Models\Games\GameTeam;
use App\Models\Games\GameTeamsGuest;
use App\Models\Games\GameTeamsPlayer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GameTeamsService
{
    private const TEAM_SCORE_DIFF_MAX = 2;
    private const TEAM_BALANCE_THRESHOLD = 21;

    private function pickTeamBySkaterCountAndSkill(int $team1SkaterCount, int $team2SkaterCount, int $team1Skill, int $team2Skill): int
    {
        // Keep skater counts as even as possible.
        if ($team1SkaterCount < $team2SkaterCount) return 1;
        if ($team2SkaterCount < $team1SkaterCount) return 2;

        // With equal skater counts, send the next skater to the lower-score team.
        if ($team1Skill < $team2Skill) return 1;
        if ($team2Skill < $team1Skill) return 2;

        // Still tied => random.
        return random_int(0, 1) === 0 ? 1 : 2;
    }

    private function pickTeamByTotalCount(int $team1Count, int $team2Count): int
    {
        if ($team1Count < $team2Count) return 1;
        if ($team2Count < $team1Count) return 2;

        return random_int(0, 1) === 0 ? 1 : 2;
    }

    private function sumMemberLevels(array $members): int
    {
        $sum = 0;
        foreach ($members as $member) {
            $sum += (int) ($member['level'] ?? 3);
        }

        return $sum;
    }

    private function buildBalancedSkaterTeams(Collection $skaterPool): array
    {
        $totalSkaters = $skaterPool->count();
        if ($totalSkaters === 0) {
            return [
                'team1' => [],
                'team2' => [],
                'team1Skill' => 0,
                'team2Skill' => 0,
            ];
        }

        $best = null;
        $bestDiff = PHP_INT_MAX;

        for ($attempt = 1; $attempt <= self::TEAM_BALANCE_THRESHOLD; $attempt++) {
            $shuffled = $skaterPool->shuffle()->values();
            $baseTeamSize = intdiv($totalSkaters, 2);

            $team1Target = $baseTeamSize;
            $team2Target = $baseTeamSize;
            if (($totalSkaters % 2) === 1) {
                if (random_int(0, 1) === 0) {
                    $team1Target++;
                } else {
                    $team2Target++;
                }
            }

            $team1 = $shuffled->slice(0, $team1Target)->values()->all();
            $team2 = $shuffled->slice($team1Target)->values()->all();

            $team1Skill = $this->sumMemberLevels($team1);
            $team2Skill = $this->sumMemberLevels($team2);
            $diff = abs($team1Skill - $team2Skill);

            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $best = [
                    'team1' => $team1,
                    'team2' => $team2,
                    'team1Skill' => $team1Skill,
                    'team2Skill' => $team2Skill,
                ];
            }

            if ($diff <= self::TEAM_SCORE_DIFF_MAX) {
                break;
            }
        }

        return $best ?? [
            'team1' => [],
            'team2' => [],
            'team1Skill' => 0,
            'team2Skill' => 0,
        ];
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

            // No existing assignments => initial locked teams.
            if ($existingUsers->isEmpty() && $existingGuests->isEmpty()) {

                $teamUsers = [1 => [], 2 => []];
                $teamGuests = [1 => [], 2 => []];

                // Team score is skater-only; goalies are excluded.
                $teamSkill = [1 => 0, 2 => 0];

                $goaliePool = collect();
                foreach ($goalieIds->all() as $id) $goaliePool->push(['type' => 'user', 'id' => (int) $id, 'level' => $userLevelMap[$id] ?? 3]);
                foreach ($guestGoalieIds->all() as $id) $goaliePool->push(['type' => 'guest', 'id' => (int) $id, 'level' => $guestLevelMap[$id] ?? 3]);
                $goaliePool = $goaliePool->shuffle()->values();

                $assignGoalie = function (int $teamNo, array $goalie) use (&$teamUsers, &$teamGuests) {
                    if ($goalie['type'] === 'user') $teamUsers[$teamNo][] = $goalie['id'];
                    else $teamGuests[$teamNo][] = $goalie['id'];
                };

                // Spread goalies across teams when possible.
                if ($goaliePool->count() >= 1) $assignGoalie(1, $goaliePool[0]);
                if ($goaliePool->count() >= 2) $assignGoalie(2, $goaliePool[1]);
                for ($i = 2; $i < $goaliePool->count(); $i++) {
                    $teamNo = $this->pickTeamByTotalCount(
                        count($teamUsers[1]) + count($teamGuests[1]),
                        count($teamUsers[2]) + count($teamGuests[2])
                    );
                    $assignGoalie($teamNo, $goaliePool[$i]);
                }

                $skaterPool = collect();
                foreach ($playerIds->all() as $id) $skaterPool->push(['type' => 'user', 'id' => (int) $id, 'level' => $userLevelMap[$id] ?? 3]);
                foreach ($guestPlayerIds->all() as $id) $skaterPool->push(['type' => 'guest', 'id' => (int) $id, 'level' => $guestLevelMap[$id] ?? 3]);

                $balancedSkaters = $this->buildBalancedSkaterTeams($skaterPool);
                foreach ([1, 2] as $teamNo) {
                    $members = $teamNo === 1 ? $balancedSkaters['team1'] : $balancedSkaters['team2'];
                    foreach ($members as $m) {
                        if ($m['type'] === 'user') $teamUsers[$teamNo][] = (int) $m['id'];
                        else $teamGuests[$teamNo][] = (int) $m['id'];
                    }
                }
                $teamSkill[1] = (int) ($balancedSkaters['team1Skill'] ?? 0);
                $teamSkill[2] = (int) ($balancedSkaters['team2Skill'] ?? 0);

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

            $teamTotalCount = function (int $teamNo) use ($game) {
                $u = (int) DB::table('game_teams_players')->where('game_id', $game->id)->where('team', $teamNo)->count();
                $g = (int) DB::table('game_teams_guests')->where('game_id', $game->id)->where('team', $teamNo)->count();
                return $u + $g;
            };

            $teamSkaterCount = function (int $teamNo) use ($game) {
                $u = (int) DB::table('game_teams_players')
                    ->join('game_players', function ($join) use ($game) {
                        $join->on('game_teams_players.user_id', '=', 'game_players.user_id')
                            ->whereColumn('game_teams_players.game_id', 'game_players.game_id')
                            ->where('game_players.game_id', $game->id)
                            ->where('game_players.role', 'player');
                    })
                    ->where('game_teams_players.game_id', $game->id)
                    ->where('game_teams_players.team', $teamNo)
                    ->count();

                $g = (int) DB::table('game_teams_guests')
                    ->join('game_players_guests', function ($join) use ($game) {
                        $join->on('game_teams_guests.guest_id', '=', 'game_players_guests.id')
                            ->where('game_players_guests.game_id', $game->id)
                            ->where('game_players_guests.role', 'player');
                    })
                    ->where('game_teams_guests.game_id', $game->id)
                    ->where('game_teams_guests.team', $teamNo)
                    ->count();

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

            // Compute current team skill sums from existing assignments (skaters only).
            $goalieUserIdLookup = array_fill_keys($goalieIds->all(), true);
            $goalieGuestIdLookup = array_fill_keys($guestGoalieIds->all(), true);
            $teamSkill = [1 => 0, 2 => 0];
            foreach ($existingUsers as $row) {
                if (isset($goalieUserIdLookup[(int) $row->user_id])) {
                    continue;
                }
                $lvl = $userLevelMap[(int) $row->user_id] ?? 3;
                $teamSkill[(int) $row->team] = ($teamSkill[(int) $row->team] ?? 0) + (int) $lvl;
            }
            foreach ($existingGuests as $row) {
                if (isset($goalieGuestIdLookup[(int) $row->guest_id])) {
                    continue;
                }
                $lvl = $guestLevelMap[(int) $row->guest_id] ?? 3;
                $teamSkill[(int) $row->team] = ($teamSkill[(int) $row->team] ?? 0) + (int) $lvl;
            }

            // Add missing goalies first, preferring any team that doesn't yet have one.
            $missingUserGoalies = $missingUserIds->intersect($goalieIds)->shuffle()->values();
            foreach ($missingUserGoalies as $uid) {
                $targetTeam = !$teamHasGoalie[1]
                    ? 1
                    : (!$teamHasGoalie[2]
                        ? 2
                        : $this->pickTeamByTotalCount($teamTotalCount(1), $teamTotalCount(2))
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
                        : $this->pickTeamByTotalCount($teamTotalCount(1), $teamTotalCount(2))
                    );
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
                $teamHasGoalie[$targetTeam] = true;
                $missingGuestIds = $missingGuestIds->diff([(int) $gid])->values();
            }

            // Add remaining missing skaters/attendees to the smaller/less-skilled team.
            foreach ($missingUserIds->shuffle()->values() as $uid) {
                $level = $userLevelMap[$uid] ?? 3;
                $targetTeam = $this->pickTeamBySkaterCountAndSkill($teamSkaterCount(1), $teamSkaterCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0);
                GameTeamsPlayer::create(['game_id' => $game->id, 'user_id' => (int) $uid, 'team' => $targetTeam]);
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
            }

            foreach ($missingGuestIds->shuffle()->values() as $gid) {
                $level = $guestLevelMap[$gid] ?? 3;
                $targetTeam = $this->pickTeamBySkaterCountAndSkill($teamSkaterCount(1), $teamSkaterCount(2), $teamSkill[1] ?? 0, $teamSkill[2] ?? 0);
                GameTeamsGuest::create(['game_id' => $game->id, 'guest_id' => (int) $gid, 'team' => $targetTeam]);
                $teamSkill[$targetTeam] = ($teamSkill[$targetTeam] ?? 0) + (int) $level;
            }
        });
    }
}
