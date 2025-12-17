<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'notificationid';
    public $timestamps = false;

    protected $fillable = [
        'userid',
        'title',
        'message',
        'notificationType',
        'isread'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}

