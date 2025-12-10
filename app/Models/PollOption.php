<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    protected $table = 'polloption';
    protected $primaryKey = 'polloptionid';
    public $timestamps = false;

    protected $fillable = [
        'pollid',
        'optiontext',
    ];

    /**
     * Get the poll that owns this option.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'pollid', 'pollid');
    }

    /**
     * Get the votes for this option.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class, 'polloptionid', 'polloptionid');
    }

    /**
     * Get the vote count for this option.
     */
    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get the percentage of votes for this option.
     */
    public function getPercentageAttribute(): float
    {
        $totalVotes = $this->poll->total_votes;
        if ($totalVotes === 0) {
            return 0.0;
        }
        return round(($this->vote_count / $totalVotes) * 100, 2);
    }
}
