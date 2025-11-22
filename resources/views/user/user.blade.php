@extends('layouts.app')

@section('content')
<style>
    /* --- Container Principal --- */
    .profile-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
    }

    /* --- Header do Perfil --- */
    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .profile-header img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #6a0dad; 
    }

    .profile-header h1 {
        font-size: 1.8em;
        margin: 0;
        color: #000; 
    }

    .profile-header span {
        color: #000; 
        font-size: 0.95em;
    }

    /* --- Botões --- */
    .profile-actions {
        margin-top: 20px;
    }

    .profile-actions .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #6a0dad;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        margin-bottom: 20px;
        transition: background-color 0.3s ease;
    }

    .profile-actions .button:hover {
        background-color: #7d1dcf; 
    }

    .profile-actions .secondary-button {
        background-color: #444; 
        margin-left: 10px;
    }

    .profile-actions .secondary-button:hover {
        background-color: #555;
    }

    /* --- Lista de Eventos --- */
    .profile-actions h2 {
        margin-bottom: 15px;
        font-size: 1.5em;
        border-bottom: 2px solid #6a0dad; /* roxo */
        padding-bottom: 5px;
        color: #000;
    }

    .events-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    /* --- Cards de Evento --- */
    .event-card {
        border-radius: 8px;
        padding: 15px;
        background: linear-gradient(135deg, #fff, #dda0dd); /* branco + roxo suave */
        color: #000;
        border: 1px solid #444;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.6);
    }

    .event-card strong {
        font-size: 1.1em;
        margin-bottom: 5px;
        color: #6a0dad; /* roxo */
    }

    .event-card p {
        flex-grow: 1;
        margin: 5px 0;
    }

    .event-card span {
        font-size: 0.85em;
        color: #555;
    }

    .view-event-btn {
        display: inline-block;
        margin-top: 10px;
        padding: 6px 12px;
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .view-event-btn:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }

    /* --- Responsivo --- */
    @media screen and (max-width: 600px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
        }
    }
</style>

<div class="profile-container">
    <!-- Header do Perfil -->
    <div class="profile-header">
        <img src="{{ $user->profileimage ?? '/images/default-profile.png' }}" alt="Foto de perfil">
        <div>
            <h1>{{ $user->username }}</h1>
            <span>{{ $user->email }}</span>
        </div>
        <a href="{{ route('user.edit') }}" class="edit-button">Edit</a>

    </div>

    <!-- Ações do Usuário -->
    <div class="profile-actions">
        @if(Auth::check() && Auth::user()->userid === $user->userid)
            <a href="{{ route('events.create') }}" class="button">Create Event</a>
        @endif

        <a href="{{ route('dashboard') }}" class="button secondary-button">Homepage</a>

        <h2>My Events</h2>

        <div class="events-list">
            @forelse($events as $event)
                <div class="event-card">
                    <strong>{{ $event->eventname }}</strong>
                    <p>{{ Str::limit($event->description, 100) }}</p>
                    <span>Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span><br>
                    <a href="{{ route('events.show', $event->eventid) }}" class="view-event-btn">View Event</a>
                </div>
            @empty
                <p style="color:#fff;">You did not create any event.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
