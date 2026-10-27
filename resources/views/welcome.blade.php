<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wevent - Event Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <!-- Hero Section -->
    <div class="flex-grow-1 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-3 fw-bold text-purple mb-3">Wevent</h1>
                    <p class="lead text-muted mb-4">
                        Create, manage, and discover events with ease. 
                        Connect with people who share your interests.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-purple btn-lg px-5">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-purple btn-lg px-5">
                                    Log In
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-purple btn-lg px-5">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <div class="bg-purple-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="text-purple fs-4">&#128197;</span>
                        </div>
                        <h5 class="text-purple">Create Events</h5>
                        <p class="text-muted">Easily create and customize events for any occasion.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <div class="bg-purple-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="text-purple fs-4">&#128101;</span>
                        </div>
                        <h5 class="text-purple">Invite People</h5>
                        <p class="text-muted">Invite participants and manage your guest list effortlessly.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <div class="bg-purple-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="text-purple fs-4">&#128269;</span>
                        </div>
                        <h5 class="text-purple">Discover Events</h5>
                        <p class="text-muted">Browse and join public events in your area.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-3 bg-purple text-white text-center">
        <div class="container">
            <small>&copy; {{ date('Y') }} Wevent</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
