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

    public function edit()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
    
        // validar inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|min:6'
        ]);
    
        // atualizar email
        $user->email = $request->email;
    
        // atualizar password se foi preenchida
        if ($request->password) {
            $user->password = $request->password;
        }
    
        $user->save();
    
        return redirect()->route('user.show')
                         ->with('success', 'Dados atualizados com sucesso!');
    }    
    
}

