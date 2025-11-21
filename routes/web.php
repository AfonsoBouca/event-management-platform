<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

// Home -> redireciona conforme autenticação
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::controller(LogoutController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
});


Route::get('/user/{id}', [UserController::class, 'showProfile'])
     ->middleware('auth')
     ->name('user.profile');

// Dashboard
Route::get('/dashboard', [EventController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

// Pesquisa de eventos públicos
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');


// Eventos públicos
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:eventid}', [EventController::class, 'show'])->name('events.show');

// Eventos (autenticados)
Route::middleware('auth')->group(function() {
    Route::get('/events/index', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});





