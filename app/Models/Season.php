<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    /**
     * Get the games for the season.
     */
    public function games()
    {
        return $this->hasMany(\App\Models\Games\Game::class, 'season_id');
    }
}
