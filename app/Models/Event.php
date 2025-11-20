<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'eventName',
        'description',
        'startdate',
        'enddate',
        'capicity',
        'price',
        'isPublic',
        'location',
        'createdBy',
        'status',
    ];

    public $timestamps = false;

    protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
    
    ];
}
