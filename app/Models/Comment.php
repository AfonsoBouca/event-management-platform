<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';
    protected $primaryKey = 'commentid';
    public $timestamps = false;

    protected $fillable = [
        'content', 'authorid', 'eventid', 'parentid'
    ];

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'authorid', 'userid');
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class, 'eventid', 'eventid'); 
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parentid', 'commentid')
                    ->with('author'); 
    }
}