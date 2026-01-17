<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_players', function (Blueprint $table) {
            // Prevent duplicate user registrations for the same game
            // A user can only be registered once per game (they can change their role, but not duplicate)
            $table->unique(['game_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_players', function (Blueprint $table) {
            $table->dropUnique(['game_id', 'user_id']);
        });
    }
};
