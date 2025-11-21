<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Wevent') }}</title>

        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript" src={{ url('js/app.js') }} defer></script>

        @stack('scripts')
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ route('events.index') }}">Wevent</a></h1>

                @auth
                    <span>{{ Auth::user()->username }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="button">Logout</button>
                    </form>
                @endauth

                @guest
                    <a class="button" href="{{ route('login') }}">Login</a>
                    <a class="button" href="{{ route('register') }}">Register</a>
                @endguest
            </header>

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