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
        // Add a global player `level` to users so admins can rank players (1-5).
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('level')->default(3)->after('role_preference');
            });
        }

        // Add a `level` for guest rows so ad-hoc guests can also be balanced.
        if (Schema::hasTable('game_players_guests')) {
            Schema::table('game_players_guests', function (Blueprint $table) {
                $table->unsignedTinyInteger('level')->default(3)->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }

        if (Schema::hasTable('game_players_guests')) {
            Schema::table('game_players_guests', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
};
