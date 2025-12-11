<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollVote extends Model
{
    protected $table = 'pollvote';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'polloptionid',
        'userid',
        'votedat',
    ];

    protected $casts = [
        'votedat' => 'datetime',
    ];

    /**
     * Get the option that was voted for.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'polloptionid', 'polloptionid');
    }

    /**
     * Get the user who voted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }
}
