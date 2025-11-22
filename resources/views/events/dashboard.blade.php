<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wevent Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* --- Top Bar --- */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color: #17a2b8;
            color: white;
        }
        .top-bar a {
            color: white;
            text-decoration: none;
        }

        /* --- Search --- */
        .search-bar {
            margin: 20px 30px;
        }
        .search-bar input {
            padding: 8px;
            width: 200px;
        }
        .search-bar button {
            padding: 8px 12px;
        }

        /* --- Browse Buttons --- */
        .browse-events {
            margin: 20px 30px;
        }
        .browse-buttons a {
            display: inline-block;
            margin: 5px 10px 5px 0;
            padding: 8px 15px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .browse-buttons a:hover {
            background-color: #138496;
        }

        /* --- Event Cards --- */
        .event-section {
            margin: 20px 30px;
        }
        .event-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .event-card strong {
            font-size: 1.1em;
        }
        .event-card p {
            margin: 5px 0;
        }
        .event-card span {
            font-size: 0.85em;
            color: #555;
        }
        .view-event-btn {
            display: inline-block;
            margin-top: 5px;
            padding: 5px 10px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.85em;
        }
        .view-event-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Barra superior -->
    <header class="top-bar">
        <div class="logo"><h2>Wevent</h2></div>
        <div class="profile">
            <a href="{{ route('user.profile', ['id' => Auth::user()->userid]) }}">
                {{ Auth::user()->username }}
            </a>
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

    <!-- Browsing -->
    <section class="browse-events">
        <h3>Browse Events</h3>
        <div class="browse-buttons">
            <a href="{{ route('events.browse', ['status' => 'upcoming']) }}">Upcoming</a>
            <a href="{{ route('events.browse', ['status' => 'ongoing']) }}">Ongoing</a>
            <a href="{{ route('events.browse', ['status' => 'completed']) }}">Completed</a>
            <a href="{{ route('events.browse', ['category' => 'Music']) }}">Music</a>
            <a href="{{ route('events.browse', ['category' => 'Tech']) }}">Technology</a>
            <a href="{{ route('events.browse', ['category' => 'Sports']) }}">Sports</a>
            <a href="{{ route('events.browse', ['category' => 'Business']) }}">Business</a>
            <a href="{{ route('events.browse', ['category' => 'Arts & Culture']) }}">Arts&Culture</a>
            <a href="{{ route('events.browse', ['category' => 'Education']) }}">Education</a>
            <a href="{{ route('events.browse', ['category' => 'Health & Wellness']) }}">Health&Wellness</a>
            <a href="{{ route('events.browse', ['category' => 'Food & Drink']) }}">Food&Drink</a>
            <a href="{{ route('events.browse', ['category' => 'Networking']) }}">Networking</a>
            <a href="{{ route('events.browse', ['category' => 'Charity']) }}">Charity</a>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="event-section">
        <h3>Upcoming Events</h3>
        @foreach($upcomingEvents->take(5) as $event)
            <div class="event-card">
                <strong>{{ $event->eventname }}</strong>
                <p>{{ Str::limit($event->description, 80) }}</p>
                <span>Starts: {{ $event->startdate->format('d/m/Y H:i') }}</span><br>
                <a class="view-event-btn" href="{{ route('events.show', ['id' => $event->eventid]) }}">View Event</a>
            </div>
        @endforeach
    </section>

    <!-- Recently Added -->
    <section class="event-section">
        <h3>Recently Added</h3>
        @foreach($recentEvents->take(5) as $event)
            <div class="event-card">
                <strong>{{ $event->eventname }}</strong>
                <p>{{ Str::limit($event->description, 80) }}</p>
                <span>Starts: {{ $event->startdate->format('d/m/Y H:i') }}</span><br>
                <a class="view-event-btn" href="{{ route('events.show', ['id' => $event->eventid]) }}">View Event</a>
            </div>
        @endforeach
    </section>
</body>
</html>
