<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Invitation;

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
              
    
        // validar inputs
        $request->validate([
            'username' =>'required|unique:user,username,' . auth()->id(). ',userid',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'profileimage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user->username = $request->username;
    
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

        $participants = $user->attendingEvents()->get();

        $pendingInvitations = Invitation::where('userid', $user->userid)
            ->where('confirmation', false)
            ->with('event')
            ->get();


        return view('user.user', compact('user', 'events', 'participants', 'pendingInvitations'));
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        $user ->isdeleted = true;
        $user->username = 'deleted_user_' . $user->userid;
        $user->email = 'deleted_user_' . $user->userid . '@deleted.local';
        $user ->profileimage = 'avatars/deleted.png';

        $user->passwordhash = bcrypt(Str::random(32));

        $user->save();

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Your account has been deleted.');
    }

    
}

