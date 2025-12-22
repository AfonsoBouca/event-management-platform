<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        
        if(Auth::guard('admin')->check()){
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('events.dashboard');
        } 
            return view('auth.login');
    }

    /**
     * Process an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
       
        $user = \App\Models\User::where('email', $credentials['email'])->first();
    
        if ($user && $user->isbanned) {
            return back()->withErrors([
                'email' => 'Your account has been banned.'
            ])->onlyInput('email');
        }
       
        if ($user && $user->isAdmin()) {
    
            if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
    
        } else {
    
            if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('events.dashboard');
            }
    
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
}
}
