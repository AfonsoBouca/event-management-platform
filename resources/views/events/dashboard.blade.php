@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Search Bar -->
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
           <a class="view-event-btn" href="{{ route('events.browse') }}?status=upcoming">Upcoming</a>
           <a class="view-event-btn" href="{{ route('events.browse') }}?status=ongoing">Ongoing</a>
           <a class="view-event-btn" href="{{ route('events.browse') }}?status=completed">Completed</a>
           <a class="view-event-btn" href="{{ route('events.browse') }}?status=cancelled">Cancelled</a>

    <!-- Categories -->
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Music">Music</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Tech">Technology</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Sports">Sports</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Business">Business</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Arts & Culture">Arts & Culture</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Education">Education</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Health & Wellness">Health & Wellness</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Food & Drink">Food & Drink</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Networking">Networking</a>
            <a class="view-event-btn" href="{{ route('events.browse') }}?category=Charity">Charity</a>
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
</div>
@endsection
