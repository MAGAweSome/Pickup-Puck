<?php

namespace App\Models\Games;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTeamsGuest extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'game_teams_guests';

    protected $fillable = [
        'game_id',
        'guest_id',
        'team',
    ];
}
