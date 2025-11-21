<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <!-- Barra superior -->
    <header class="top-bar">
        <div class="logo">
            <h2>Wevent</h2>
        </div>
        <div class="profile">
                <span>{{ Auth::user()->name }}</span>
            </a>
        </div>
    </header>

    <header class="dashboard-header">
     <div class="user-info"> 

            <!-- Logout button -->
              <form action="{{ route('logout') }}" method="POST" style="display:inline"> 
                @csrf
                 <button type="submit">Logout</button>
                 </form> 
                </div> 
            </header>

    <!-- Barra de pesquisa -->
    <section class="search-bar">
        <form action="{{ route('events.search') }}" method="GET">
            <input type="text" name="query" placeholder="Search Events..." required>
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- Listas de eventos -->
    <main class="event-lists">
        <section class="upcoming-events">
            <h3>Upcoming Events</h3>
            <ul>
                @foreach($upcomingEvents as $event)
                    <li>
                        <strong>{{ $event->eventname }}</strong>
                        <span>{{ $event->startdate->format('d/m/Y H:i') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="recent-events">
            <h3>Recently Added</h3>
            <ul>
                @foreach($recentEvents as $event)
                    <li>
                        <strong>{{ $event->eventname }}</strong>
                        <span>Coming on {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
</body>
</html>
