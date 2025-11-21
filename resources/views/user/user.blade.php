<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <style>
        .profile-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 24px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        .profile-header img {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .profile-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .button-group a {
            padding: 10px 16px;
            border-radius: 8px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        .button-group a:hover {
            background-color: #3730a3;
        }
    </style>
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

        <div class="profile-actions button-group">
            @auth
                @if(Auth::id() === $user->userid)
                    <a href="{{ route('events.index') }}">Criar Evento</a>
                @endif
            @endauth

           
        </div>
    </div>
</body>
</html>

