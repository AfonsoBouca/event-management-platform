<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    // Mostrar formulário
    public function show(string $token)
    {
        $record = DB::table('event_platform.password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('login')
                ->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        return view('auth.resetPassword', [
            'token' => $token,
            'email' => $record->email,
        ]);
    }

    // Atualizar password
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('event_platform.password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Atualizar password
        User::where('email', $record->email)->update([
            'passwordhash' => Hash::make($request->password),
        ]);

        // Invalidar token
        DB::table('event_platform.password_reset_tokens')
            ->where('email', $record->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully.');
    }
}
