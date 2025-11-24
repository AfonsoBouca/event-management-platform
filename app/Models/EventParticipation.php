<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
    public $timestamps = false;
    protected $table = 'eventparticipation';

    protected $fillable = [
        'userid',
        'eventid',
        'role'
    ];
}
