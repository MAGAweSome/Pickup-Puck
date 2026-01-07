<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameDefault extends Model
{
    protected $table = 'game_defaults';
    protected $fillable = [
        'default_time',
        'default_location',
        'default_duration',
        'default_price',
        'default_season_id',
        'default_title_template',
    ];

}
