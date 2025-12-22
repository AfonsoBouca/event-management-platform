<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMessage extends Model
{
    protected $table = 'eventmessage';
    protected $primaryKey = 'messageid';
    public $timestamps = false;

    protected $fillable = [
        'eventid',
        'authorid',
        'content'
    ];

    public function author() {
        return $this->belongsTo(User::class, 'authorid');
    }

    public function event() {
        return $this->belongsTo(Event::class, 'eventid');
    }
}
