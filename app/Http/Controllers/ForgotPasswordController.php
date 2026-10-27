<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function send(Request $request)
    {
         //dd('ForgotPasswordController@send reached');

        $request->validate([
            'email' => 'required|email',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with that email.'
            ]);
        }
    
        // gerar token
        $token = Str::random(64);
    
        DB::table('event_platform.password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );
    
        $mailData = [
            'name' => $user->username,
            'reset_link' => url('/reset-password/' . $token),
        ];
    
        Mail::to($user->email)->send(
            new ForgotPasswordMail($mailData)
        );
    
        return back()->with('success', 'Reset link sent to your email.');
    }

}