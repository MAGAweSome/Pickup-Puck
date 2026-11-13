<?php

namespace App\Models\Games;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AfterGameEvent extends Model
{
    use HasFactory;

    protected $table = 'after_game_events';

    protected $fillable = [
        'game_id',
        'creator_id',
        'title',
        'location',
        'description',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'after_game_event_attendees', 'after_game_event_id', 'user_id')
            ->withPivot('note')
            ->withTimestamps();
    }

    public function isUserAttending(int $userId): bool
    {
        return $this->attendees()->where('users.id', $userId)->exists();
    }
}
