<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head> 
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="{{ $user->profileimage ?? '/images/default-profile.png' }}" alt="Foto de perfil">
            <div>
                <h1>{{ $user->username }}</h1>
                <span>{{ $user->email }}</span>
            </div>
        </div>

        <div class="profile-actions">
            @if(Auth::check() && Auth::user()->userid === $user->userid)
                <a href="{{ route('events.create') }}" class="button">Criar Evento</a>
            @endif

            <h1>Eventos que estou organizando</h1>

              @forelse($events as $event)
                <p>{{ $event->eventname }} - {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y') }}</p>
                @empty
              <p>Você ainda não está organizando nenhum evento.</p>
             @endforelse

   </div>

           
        </div>
    </div>
</body>
</html>

