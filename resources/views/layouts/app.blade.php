<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Wevent') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS customizado -->
    <link href="{{ url('css/app.css') }}" rel="stylesheet">

    {{-- Scripts específicos das páginas --}}
    @stack('scripts')
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    {{-- 🔹 USER_ID disponível para o JS --}}
    @php
        $currentUser = Auth::user() ?? auth('admin')->user();
        $isAdmin = auth('admin')->check();
    @endphp

    @if($currentUser && !$isAdmin)
        <script>
            window.USER_ID = {{ $currentUser->userid }};
        </script>
    @endif

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-purple">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}">Wevent</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    @if($currentUser)
                        {{-- Notificações só para usuários normais --}}
                        @if(!$isAdmin)
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="{{ route('notifications.view') }}"> 
                                Notifications
                            </a>    
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="{{ $isAdmin ? route('admin.dashboard') : route('user.profile', $currentUser->username) }}">
                                {{ $isAdmin ? 'Admin' : $currentUser->username }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <form method="POST" action="{{ $isAdmin ? route('admin.logout') : route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Toast de notificações só para usuários normais --}}
    @if(!$isAdmin)
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notificationToast" class="toast align-items-center text-bg-primary border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">
                    Nova notificação
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    @endif

    <footer class="bg-white border-top mt-auto">
        <div class="container py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="text-muted small">
                © {{ date('Y') }} Wevent 
            </span>

            <div class="d-flex gap-3">
                <a href="{{ route('about') }}" class="text-decoration-none text-muted small">
                    About
                </a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS compilado (Vite + Echo + Pusher + listeners) --}}
    @vite(['resources/js/app.js'])
    
    <script>
        console.log('INLINE SCRIPT WORKS');
    </script>

</body>
</html>
