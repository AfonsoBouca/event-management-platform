<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Show the user registration form.
     */
    public function showRegistrationForm(): View
    {
        // Render the registration view.
        return view('auth.register');
    }

    /**
     * Handle a new user registration request.
     *
     * This method:
     * - Validates the registration input data.
     * - Creates a new user with a hashed password.
     * - Logs the user in automatically after registration.
     * - Regenerates the session to prevent fixation attacks.
     * - Redirects the user to the cards page with a success message.
     */
    public function register(Request $request): RedirectResponse
    {
    $credentials = $request->validate([
        'username' => ['required', 'string', 'max:50', 'unique:user,username'],
        'email' => ['required', 'email', 'max:100', 'unique:user,email'],
        'password' => ['required', 'confirmed', 'min:6'],
    ]);

    $user = User::create([
        'username' => $credentials['username'],
        'email' => $credentials['email'],
        'passwordhash' => bcrypt($credentials['password']),
        'isadmin' => false,
    ]);

    Auth::login($user);

    return redirect()->route('events.index');
    }
}
