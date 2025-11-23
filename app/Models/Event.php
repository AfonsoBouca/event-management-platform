<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';
    protected $primaryKey = 'eventid';  

    protected $fillable = [
        'eventname',
        'description',
        'startdate',
        'enddate',
        'capacity',
        'price',
        'ispublic',
        'location',
        'createdby',
        'eventstatus',
    ];

    public $timestamps = false;

    protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
        'ispublic' => 'boolean',
    ];

    public function isOrganizer(int $userId): bool
    {
        return $this->createdby === $userId;
    }

    public function organizer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'createdby', 'userid');
    }

    /**
     * Get all attendees for this event.
     */
    public function attendees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'eventparticipation',
            'eventid',
            'userid'
        )->withPivot('role');
    }

    /**
     * Check if a user is an attendee of this event.
     */
    public function isAttendee(int $userId): bool
    {
        return $this->attendees()->where('eventparticipation.userid', $userId)->exists();
    }

    /**
     * Get all join requests for this event.
     */
    public function joinRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\JoinRequest::class, 'eventid', 'eventid');
    }
}
