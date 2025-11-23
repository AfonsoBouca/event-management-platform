<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Wevent') }}</title>

    <!-- CSS principal -->
    <link href="{{ url('css/app.css') }}" rel="stylesheet">

    <style>
        /* --- Header --- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color:rgb(255, 253, 253); /* Preto */
            color: #ffffff; 
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
            color:rgb(255, 255, 255); 
        }

        /*header h1 a {
            text-decoration: none;
            color: inherit;
            pointer-events: none;
        }*/

        header .user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        header .user-actions a {
            background-color: #6a0dad; /* Roxo */
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        header .user-actions a:hover {
            background-color: #7d1dcf; /* Roxo claro */
        }

        header .user-actions button {
            background-color: #6a0dad;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        header .user-actions button:hover {
            background-color: #7d1dcf;
        }

        /* --- Alerts --- */
        .alert {
            border-radius: 5px;
            padding: 10px 15px;
            margin: 15px 30px;
            color: #fff;
        }

        .alert-success {
            background-color: #28a745;
        }

        .alert-danger {
            background-color: #dc3545;
        }

        /* --- Main Content --- */
        #content {
            margin: 20px 30px;
            font-family: Arial, sans-serif;
            color:rgb(0, 0, 0);
            background-color:rgb(255, 255, 255); 
            padding: 20px;
            border-radius: 8px;
        }
        /* --- Event Cards --- */
        .event-card {
            background-color:rgb(255, 255, 255); 
            color:rgb(0, 0, 0);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #444;
        }

        .event-card strong {
            font-size: 1.2em;
            color: #dda0dd; /* Roxo suave */
        }

        .event-card p {
            margin: 5px 0;
        }

        .event-card span {
            font-size: 0.85em;
            color: #ccc;
        }

        /* --- Buttons --- */
        .view-event-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #6a0dad;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .view-event-btn:hover {
            background-color: #7d1dcf;
        }

        /* --- Search Bar --- */
        .search-bar {
            margin: 20px 30px;
        }

        .search-bar input {
            padding: 8px;
            width: 200px;
        }

        .search-bar button {
            padding: 8px 12px;
            background-color: #6a0dad;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #7d1dcf;
        }
    </style>

    @stack('scripts')
</head>
<body>
    <main>
        <!-- Header -->
        <header>
            <h1><a href="{{ route('dashboard') }}">Wevent</a></h1>
            <div class="user-actions">
                @auth
                <a href="{{ route('user.profile', Auth::user()->username) }}">             <!-- está a ser carregado em /register mesmo quando não existe um user -->
                        {{ Auth::user()->username }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endguest
            </div>
        </header>

        <!-- Content -->
        <section id="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</body>
</html>
