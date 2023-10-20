<?php

namespace App\Models\Games;

use App\Enums\Games\GameRoles;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'time' => 'datetime',
    ];

    /**
     * Get the players for the game.
     */
    public function gamePlayers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_players')->withPivot('role');
    }

    public function gamePayments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_payments')->withPivot('payment');
    }

    public function gameTeamsPlayers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_teams_players')->withPivot('team');
    }

    protected function players(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->gamePlayers()->wherePivot('role', GameRoles::Player)->get()
        );
    }

    protected function goalies(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->gamePlayers()->wherePivot('role', GameRoles::Goalie)->get()
        );
    }

    // protected function referees(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => $this->gamePlayers()->wherePivot('role', GameRoles::Referee)->get()
    //     );
    // }

    protected function gameTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->time->isoFormat('dddd, MMM D @ h:mma')
        );
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
