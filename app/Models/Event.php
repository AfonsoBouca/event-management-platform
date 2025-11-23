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

    public function organizer()
    {
        return $this->belongsTo(\App\Models\User::class, 'createdby', 'userid');
    }

    public function attendees()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'eventparticipation',
            'eventid',
            'userid'
        )->withPivot('role');
    }

    public function isAttendee($userId)
    {
    return $this->attendees()->where('eventparticipation.userid', $userId)->exists();
    }

    public function joinRequests()
    {
        return $this->hasMany(\App\Models\JoinRequest::class, 'eventid', 'eventid');
    }
}
