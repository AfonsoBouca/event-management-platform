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

    // Relacionamento com o usuário organizador
    public function organizer()
    {
        return $this->belongsTo(\App\Models\User::class, 'createdby', 'userid');
    }

    // Relacionamento com favoritos

}
