<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class UserController extends Controller
{
    // Perfil do usuário autenticado
    public function show()
    {
        $user = Auth::user();
    
        // Pega eventos do usuário logado
        $events = Event::where('createdby', $user->userid)->get();
    
        return view('user.user', compact('user', 'events'));
    }
    
    public function showProfile($id)
    {
        $user = User::findOrFail($id);
    
        // Pega eventos do usuário do perfil
        $events = Event::where('createdby', $user->userid)->get();
    
        return view('user.user', compact('user', 'events'));
    }
}

