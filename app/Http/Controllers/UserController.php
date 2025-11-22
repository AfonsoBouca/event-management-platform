<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        //dd('UPDATE FOI CHAMADO');

        $user = Auth::user();

        if ($user->profileimage === null) {
            $user->profileimage = 'avatars/default.png';
        }
        

        if ($request->hasFile('profileimage')) {

            // apagar imagem antiga (desde que não seja a default!)
            if ($user->profileimage !== 'avatars/default.png') {
                Storage::disk('public')->delete($user->profileimage);
            }
        
            // guardar nova imagem
            $path = $request->file('profileimage')->store('avatars', 'public');
        
            // atualizar utilizador
            $user->profileimage = $path;
        }        
    
        // validar inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'profileimage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
    
        // atualizar email
        $user->email = $request->email;
    
        // atualizar password se foi preenchida
        if ($request->password) {
            $user->password = $request->password;
        }
    
        $user->save();
        //Auth::login($user);
    
        return redirect()
            ->route('user.profile', $user->username)
            ->with('success', 'Dados atualizados com sucesso!');
    }  
    
    public function showByUsername($username)
{
    $user = User::where('username', $username)->firstOrFail();

    $events = Event::where('createdby', $user->userid)->get();

    return view('user.user', compact('user', 'events'));
}

    
}

