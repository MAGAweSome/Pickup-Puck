<?php

namespace App\Models\Games;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTeamsPlayer extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'game_teams_players';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'game_id',
        'user_id',
        'team',
    ];
}
