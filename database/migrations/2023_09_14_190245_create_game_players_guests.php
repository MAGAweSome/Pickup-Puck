<?php

use App\Enums\Games\GameRoles;
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

        Schema::create('game_players_guests', function (Blueprint $table) {
            $table->string('name');
            $table->unsignedBigInteger('game_id');
            $table->enum('role', array_column(GameRoles::cases(), 'value'));
        });

        Schema::table('game_players_guests', function (Blueprint $table) {
            $table->foreign('game_id')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_players_guests');
    }
};
