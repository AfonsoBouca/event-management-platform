<?php

use App\Http\Controllers\CommentController;
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
        : redirect()->route('events.dashboard');
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
    ->name('dashboard');

// Pesquisa de eventos públicos

Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::get('/ajax/search', [EventController::class, 'ajaxSearch'])->name('events.ajaxSearch');

Route::get('/events/browse', [EventController::class, 'browse'])->name('events.browse');
Route::get('/events/dashboard', [EventController::class, 'dashboard'])->name('events.dashboard');


// Página de criar evento
Route::get('/events/create', [EventController::class, 'create'])
    ->middleware('auth')
    ->name('events.create');

// Salvar evento
Route::post('/events', [EventController::class, 'store'])
    ->middleware('auth')
    ->name('events.store');
 
Route::get('/events', [EventController::class, 'browse'])->name('events.index');

Route::get('/events/browse', [EventController::class, 'browse'])->name('events.browse');

// Ver evento específico
Route::get('/events/{id}', [EventController::class, 'show'])
       ->where('id', '[0-9]+')
       ->name('events.show');

// Comentar Eventos       
Route::post('/events/{eventId}/comments', [CommentController::class, 'store'])
      ->middleware('auth')
      ->name('comments.store'); 

Route::get('/events/{eventId}/comments', [CommentController::class, 'index'])
      ->name('comments.index');      

// Pedir para entrar no evento
Route::post('/events/{id}/join', [EventController::class, 'requestToJoin'])
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


 //apagar conta
Route::delete('/user/delete', [UserController::class, 'destroy'])
    ->middleware('auth')
    ->name('user.delete');

 //apagar evento
 Route::delete('/events/{id}', [EventController::class, 'deleteEvent'])
    ->middleware('auth')
    ->name('events.deleteEvent');

 //editar evento
 Route::get('/events/{id}/edit', [EventController::class, 'editEvent'])
    ->middleware('auth')
    ->name('events.editEvent');
    
Route::put('/events/{id}', [EventController::class, 'updateEvent'])
    ->middleware('auth')
    ->name('events.updateEvent');

// Entrar em página de pesquisa
Route::get('/events/{id}/invite/users', [EventController::class, 'inviteSearch'])
    ->middleware('auth')
    ->name('events.invite.search');
    
// Página onde organizador procura utilizadores
Route::get('/events/{id}/invite/search', [EventController::class, 'searchUsers'])
    ->middleware('auth')
    ->name('events.searchUsers');

// Enviar convite
Route::post('/events/{eventid}/invite/{userid}', [EventController::class, 'inviteUser'])
    ->middleware('auth')
    ->name('events.inviteUser');

// Accept / Reject Invitations
Route::post('/invitation/{invitationid}/accept', [EventController::class, 'acceptInvitation'])
    ->middleware('auth')
    ->name('invitation.accept');

Route::post('/invitation/{invitationid}/reject', [EventController::class, 'rejectInvitation'])
    ->middleware('auth')
    ->name('invitation.reject');


   

