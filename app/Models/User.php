<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'userid';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'passwordhash',
        'profileimage',
        'isadmin'
    ];

    protected $hidden = [
        'passwordhash',
        'remember_token',
    ];

    protected $casts = [
        'isadmin' => 'boolean',
        'registrationdate' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    public function getAuthPasswordName()
    {
    return 'passwordhash';
    }

    public function getAuthIdentifierName()
    {
        return 'email';
    }


    public function organizing()
    {
        return $this->hasMany(\App\Models\Event::class, 'createdby', 'userid');
    }
   
}