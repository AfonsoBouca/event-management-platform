<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <style>
        /* --- Container Principal --- */
        .profile-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            color: #333;
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
            border: 3px solid #17a2b8;
        }

        .profile-header h1 {
            font-size: 1.8em;
            margin: 0;
        }

        .profile-header span {
            color: #666;
            font-size: 0.95em;
        }

        /* --- Ações do Usuário --- */
        .profile-actions {
            margin-top: 20px;
        }

        /* Botão de criar evento */
        .profile-actions .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .profile-actions .button:hover {
            background-color: #138496;
        }

        /* --- Lista de Eventos --- */
        .profile-actions h2 {
            margin-bottom: 15px;
            font-size: 1.5em;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 5px;
        }

        /* Container dos cards de eventos */
        .events-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        /* Card de cada evento */
        .event-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
            box-shadow: 0px 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-card strong {
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .event-card p {
            flex-grow: 1;
            margin: 5px 0;
            color: #555;
            font-size: 0.95em;
        }

        .event-card span {
            font-size: 0.85em;
            color: #777;
        }

        .view-event-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .view-event-btn:hover {
            background-color: #218838;
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
</head>
<body>
    <div class="profile-container">
        <!-- Header do Perfil -->
        <div class="profile-header">
            <img src="{{ $user->profileimage ?? '/images/default-profile.png' }}" alt="Foto de perfil">
            <div>
                <h1>{{ $user->username }}</h1>
                <span>{{ $user->email }}</span>
            </div>
        </div>

        <!-- Ações do Usuário -->
        <div class="profile-actions">
            @if(Auth::check() && Auth::user()->userid === $user->userid)
                <a href="{{ route('events.create') }}" class="button">Create Event</a>
            @endif
            
        <a href="{{ route('dashboard') }}" class="button" style="background-color:#6c757d; margin-left:10px;">Homepage</a>

         
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
                    <p>You did not create any event.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
