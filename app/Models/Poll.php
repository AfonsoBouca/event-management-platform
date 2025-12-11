<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $table = 'poll';
    protected $primaryKey = 'pollid';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'content',
        'createdat',
        'eventid',
    ];

    protected $casts = [
        'createdat' => 'datetime',
    ];

    /**
     * Get the event that owns the poll.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'eventid', 'eventid');
    }

    /**
     * Get the options for this poll.
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class, 'pollid', 'pollid');
    }

    /**
     * Get the total number of votes for this poll.
     */
    public function getTotalVotesAttribute(): int
    {
        return $this->options->sum(function ($option) {
            return $option->votes()->count();
        });
    }

    /**
     * Check if a user has already voted in this poll.
     */
    public function hasUserVoted(int $userId): bool
    {
        return PollVote::whereIn('polloptionid', $this->options->pluck('polloptionid'))
            ->where('userid', $userId)
            ->exists();
    }

    /**
     * Get the user's vote for this poll.
     */
    public function getUserVote(int $userId): ?PollVote
    {
        return PollVote::whereIn('polloptionid', $this->options->pluck('polloptionid'))
            ->where('userid', $userId)
            ->first();
    }
}
