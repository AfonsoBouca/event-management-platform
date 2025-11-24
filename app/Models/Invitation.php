<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    public $timestamps = false;

    protected $table = 'invitation';
    protected $primaryKey = 'invitationid';

    protected $fillable = [
        'eventid',
        'userid',
        'status'
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'eventid');
    }

    public function user() {
        return $this->belongsTo(User::class, 'userid');
    }
}
