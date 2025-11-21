<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Perfil do usuário autenticado
    public function show()
    {
        $user = Auth::user();
        return view('user.user', compact('user'));
    }

    // Perfil do usuário por ID (rota com {userid})
    public function showProfile($id)
    {
        $user = User::findOrFail($id); // busca usuário pelo ID
        return view('user.user', compact('user'));
    }
}

