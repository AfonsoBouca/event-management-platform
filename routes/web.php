<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

// Home -> redireciona conforme autenticação
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('events.index');
    }
    return redirect()->route('login');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Events (públicos)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:eventid}', [EventController::class, 'show'])->name('events.show');

// Events (autenticados)
Route::middleware('auth')->group(function() {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

// User Profile Page
// Route::get('/user', function () {
//     return view('user/user');   
// });
Route::get('/user', [UserController::class, 'show'])->name('user.show');

