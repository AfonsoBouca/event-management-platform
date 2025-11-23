<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'categoryid';

    public $timestamps = false;

    // Relação inversa many-to-many com eventos
    public function events()
    {
        return $this->belongsToMany(
            \App\Models\Event::class,
            'eventcategory', 
            'categoryid',    
            'eventid'        
        );
    }
}
