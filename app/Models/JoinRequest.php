<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    protected $table = 'joinrequest';
    protected $primaryKey = 'joinid';
    
    public $timestamps = false;

    protected $fillable = [
        'j_status',
        'userid',
        'eventid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'eventid', 'eventid');
    }
}