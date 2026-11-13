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
        Schema::create('after_game_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable(); // e.g. "Post-Game Hangout" or venue name
            $table->string('location'); // Venue name/address, e.g. "Beertown", "Boston Pizza"
            $table->string('description', 500)->nullable(); // Optional notes or arrival time
            $table->timestamps();
        });

        Schema::create('after_game_event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('after_game_event_id')->constrained('after_game_events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('note', 100)->nullable();
            $table->timestamps();

            // Prevent duplicate signup for the same user on the same after-game event
            $table->unique(['after_game_event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('after_game_event_attendees');
        Schema::dropIfExists('after_game_events');
    }
};
