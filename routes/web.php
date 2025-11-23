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

// Dashboard
Route::get('/dashboard', [EventController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

// Pesquisa de eventos públicos
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');

// Eventos (autenticados)
Route::middleware('auth')->group(function() {
    Route::get('/events/dashboard', [EventController::class, 'dashboard'])->name('events.dashboard');
});

// Página de criar evento
Route::get('/events/create', [EventController::class, 'create'])
    ->middleware('auth')
    ->name('events.create');

// Salvar evento
Route::post('/events', [EventController::class, 'store'])
    ->middleware('auth')
    ->name('events.store');

Route::get('/events/browse', [EventController::class, 'browse'])->name('events.browse');

// Ver evento específico
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// Pedir para entrar no evento
Route::post('/events/{id}/join', [EventController::class, 'requestToJoin'])
    ->middleware('auth')
    ->name('events.join');

// Editar Perfil
Route::get('/user/edit', [UserController::class, 'edit'])
    ->middleware('auth')
    ->name('user.edit');

Route::get('/user/{username}', [UserController::class, 'showByUsername'])
    ->middleware('auth')
    ->name('user.profile');

// Atualizar Perfil
Route::post('/user/update', [UserController::class, 'update'])
    ->middleware('auth')
    ->name('user.update');

// Gestão de Participação em Eventos (apenas organizadores)
Route::get('/events/{id}/manage', [EventController::class, 'manageParticipation'])
    ->middleware('auth')
    ->name('events.manage');

Route::post('/events/{eventId}/join-requests/{joinId}/approve', [EventController::class, 'approveJoinRequest'])
    ->middleware('auth')
    ->name('events.join-requests.approve');

Route::post('/events/{eventId}/join-requests/{joinId}/reject', [EventController::class, 'rejectJoinRequest'])
    ->middleware('auth')
    ->name('events.join-requests.reject');

Route::delete('/events/{eventId}/participants/{userId}', [EventController::class, 'removeParticipant'])
    ->middleware('auth')
    ->name('events.participants.remove');

Route::get('/user/{id}/organizing', [EventController::class, 'organizing'])
    ->where('userid', '[0-9]+')
    ->name('user.organizing');