<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report'; 

    protected $primaryKey = 'reportid'; 
    public $incrementing = true; 
    protected $keyType = 'int';

    protected $fillable = [
        'userid',
        'description',
        'reporttime',
        'reporttype',
        'status',
    ];

    protected $casts = [
        'reporttime' => 'datetime',
    ];

    public $timestamps = false;

    // Relacionamento: usuário que reportou
    public function reporter()
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    // Relacionamento com o evento reportado (opcional)
    public function event()
    {
        return $this->belongsToMany(
            Event::class,
            'eventreport',
            'reportid',
            'eventid'
        );
    }

    // Relacionamento com comentário reportado (opcional)
    public function comment()
    {
        return $this->belongsToMany(
            Comment::class,
            'commentreport',
            'reportid',
            'commentid'
        );
    }

    // Relacionamento com usuário reportado (opcional)
    public function reportedUser()
    {
        return $this->belongsToMany(
            User::class,
            'userreport',
            'reportid',
            'userid'
        );
    }
    
}
