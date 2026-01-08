<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('game_players_guests')) {
            return;
        }

        // De-dupe any existing rows so the unique index can be added safely.
        // If there are duplicates, prefer keeping the one currently referenced by game_teams_guests (if any).
        $dupes = DB::table('game_players_guests')
            ->select('game_id', 'name', DB::raw('COUNT(*) as c'))
            ->groupBy('game_id', 'name')
            ->having('c', '>', 1)
            ->get();

        foreach ($dupes as $dup) {
            $ids = DB::table('game_players_guests')
                ->where('game_id', $dup->game_id)
                ->where('name', $dup->name)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (count($ids) <= 1) {
                continue;
            }

            $keepId = null;
            if (Schema::hasTable('game_teams_guests')) {
                foreach ($ids as $id) {
                    $hasTeamRow = DB::table('game_teams_guests')
                        ->where('game_id', $dup->game_id)
                        ->where('guest_id', $id)
                        ->exists();

                    if ($hasTeamRow) {
                        $keepId = $id;
                        break;
                    }
                }
            }

            $keepId = $keepId ?? $ids[0];

            DB::table('game_players_guests')
                ->where('game_id', $dup->game_id)
                ->where('name', $dup->name)
                ->where('id', '<>', $keepId)
                ->delete();
        }

        Schema::table('game_players_guests', function (Blueprint $table) {
            $table->unique(['game_id', 'name'], 'game_players_guests_game_id_name_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('game_players_guests')) {
            return;
        }

        Schema::table('game_players_guests', function (Blueprint $table) {
            $table->dropUnique('game_players_guests_game_id_name_unique');
        });
    }
};
