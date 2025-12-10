<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $primaryKey = 'tagid';

    public $timestamps = false;

    public function events()
    {
        return $this->belongsToMany(
            \App\Models\Event::class,
            'eventtag', 
            'tagid', 
            'eventid'
        );
    }
}
