<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\EventFileController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\NotificationController;
use App\Events\NotificationCreated;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EventMessageController;

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/events', [DashboardController::class, 'browseEvents'])->name('browse');
});

Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
Route::patch('/admin/reports/{id}/update-status', [ReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('banned-users', [UserController::class, 'bannedUsers'])->name('admin.banned.users');
    Route::patch('user/{id}/unban', [UserController::class, 'unbanUser'])->name('admin.user.unban');
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

// Ver evento específico
Route::get('/events/{id}', [EventController::class, 'show'])
       ->where('id', '[0-9]+')
       ->name('events.show');

// Comentarios      
Route::post('/events/{eventId}/comments', [CommentController::class, 'store'])
      ->middleware(['auth:web,admin'])
      ->name('comments.store'); 

Route::put('/comments/{comment}',[CommentController::class, 'editComment']) 
       ->middleware('auth')
       ->name('comments.edit');

Route::delete('/comments/{comment}', [CommentController::class, 'deleteComment'])
       ->middleware(['auth:web,admin'])
       ->name('comments.delete');

Route::get('/events/{eventId}/comments', [CommentController::class, 'index'])
      ->name('comments.index');   

Route::post('/comments/{comment}/vote', [CommentController::class, 'vote'])
      ->middleware('auth')
      ->name('comments.vote');
      

// Pedir para entrar no evento
Route::post('/events/{id}/join', [EventController::class, 'requestToJoin'])
    ->name('events.join');

// Editar Perfil
Route::get('/user/edit', [UserController::class, 'edit'])
    ->middleware('auth')
    ->name('user.edit');

Route::get('/user/{username}', [UserController::class, 'showByUsername'])
    ->middleware(['auth:web,admin'])
    ->name('user.profile');

// Atualizar Perfil
Route::post('/user/update', [UserController::class, 'update'])
    ->middleware('auth')
    ->name('user.update');

//Atualizar Imagens
Route::post('/file/upload', [FileController::class, 'upload'])
    ->name('file.upload');

// Gestão de Participação em Eventos (apenas organizadores)
Route::get('/events/{id}/manage', [EventController::class, 'manageParticipation'])
    ->middleware(['auth:web,admin'])
    ->name('events.manage');

Route::post('/events/{eventId}/join-requests/{joinId}/approve', [EventController::class, 'approveJoinRequest'])
    ->middleware(['auth:web,admin'])
    ->name('events.join-requests.approve');

Route::post('/events/{eventId}/join-requests/{joinId}/reject', [EventController::class, 'rejectJoinRequest'])
    ->middleware(['auth:web,admin'])
    ->name('events.join-requests.reject');

Route::delete('/events/{eventId}/participants/{userId}', [EventController::class, 'removeParticipant'])
    ->middleware(['auth:web,admin'])
    ->name('events.participants.remove');

Route::delete('/events/{eventId}/leave', [EventController::class, 'leaveEvent'])
    ->middleware('auth')
    ->name('events.leave');

Route::get('/user/{id}/organizing', [EventController::class, 'organizing'])
    ->where('id', '[0-9]+')
    ->name('user.organizing');


 //apagar conta
Route::delete('/user/delete', [UserController::class, 'destroy'])
    ->middleware(['auth:web,admin'])
    ->name('user.delete');

 //apagar evento
 Route::delete('/events/{id}', [EventController::class, 'deleteEvent'])
    ->middleware(['auth:web,admin'])
    ->name('events.deleteEvent');

 //cancelar evento
 Route::post('/events/{id}/cancel', [EventController::class, 'cancelEvent'])
    ->middleware(['auth:web,admin'])
    ->name('events.cancel');

 //editar evento
 Route::get('/events/{id}/edit', [EventController::class, 'editEvent'])
    ->middleware(['auth:web,admin'])
    ->name('events.editEvent');
    
Route::put('/events/{id}', [EventController::class, 'updateEvent'])
    ->middleware(['auth:web,admin'])
    ->name('events.updateEvent');

// Entrar em página de pesquisa
Route::get('/events/{id}/invite/users', [EventController::class, 'inviteSearch'])
    ->middleware(['auth:web,admin'])
    ->name('events.invite.search');
    
// Página onde organizador procura utilizadores
Route::get('/events/{id}/invite/search', [EventController::class, 'searchUsers'])
    ->middleware(['auth:web,admin'])
    ->name('events.searchUsers');

// Enviar convite
Route::post('/events/{eventid}/invite/{userid}', [EventController::class, 'inviteUser'])
    ->middleware(['auth:web,admin'])
    ->name('events.inviteUser');

// Accept / Reject Invitations
Route::post('/invitation/{invitationid}/accept', [EventController::class, 'acceptInvitation'])
    ->middleware('auth')
    ->name('invitation.accept');

Route::post('/invitation/{invitationid}/reject', [EventController::class, 'rejectInvitation'])
    ->middleware('auth')
    ->name('invitation.reject');

// Event Photos
Route::get('/events/{id}/photos', [EventFileController::class, 'list'])
    ->name('events.photos');

Route::post('/events/{id}/photos/upload', [EventFileController::class, 'upload'])
    ->middleware('auth')
    ->name('events.photos.upload');

Route::delete('/photos/{photoid}', [EventFileController::class, 'delete'])
    ->middleware('auth')
    ->name('events.photos.delete');


// Ver participantes
Route::get('/events/{id}/viewParticipants', [EventController::class, 'viewParticipation'])
    ->name('events.viewParticipants');


// Polls
Route::get('/events/{id}/polls', [PollController::class, 'index'])
    ->middleware(['auth:web,admin'])
    ->name('polls.index');

Route::get('/events/{id}/polls/create', [PollController::class, 'create'])
    ->middleware('auth')
    ->name('polls.create');

Route::post('/events/{id}/polls', [PollController::class, 'store'])
    ->middleware('auth')
    ->name('polls.store');

Route::post('/polls/{id}/vote', [PollController::class, 'vote'])
    ->middleware('auth')
    ->name('polls.vote');

Route::get('/polls/{id}/results', [PollController::class, 'results'])
    ->middleware('auth')
    ->name('polls.results');

Route::delete('/polls/{id}', [PollController::class, 'destroy'])
    ->middleware('auth')
    ->name('polls.destroy');

Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifications.view');

Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
    ->middleware('auth')
    ->name('notifications.read');



Route::get('/pusher-nuclear-test', function () {
    event(new NotificationCreated([
        'userId' => auth()->user()->userid,
        'title' => 'NUCLEAR TEST',
        'message' => 'If you see this, broadcastOn ran'
    ]));

    return 'ok';
})->middleware('auth');

Route::view('/about', 'about')->name('about');


Route::get('/forgot-password', function () {
    return view('auth.forgotPassword');
})->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'update'])
    ->name('password.update');
// Event Messages
Route::get('/events/{id}/messages', [EventMessageController::class, 'index'])
    ->middleware(['auth:web,admin'])
    ->name('events.messages');

Route::post('/events/{id}/messages', [EventMessageController::class, 'store'])
    ->middleware('auth')
    ->name('events.messages.store');



