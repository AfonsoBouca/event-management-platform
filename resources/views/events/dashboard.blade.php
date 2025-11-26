@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Search Bar -->
    <section class="search-bar" style="position:relative;">
    <input id="ajax-search" type="text" placeholder="Search Events..." autocomplete="off" style="width:100%; padding:10px;">
    <div id="search-results" style="position:absolute; top:100%; left:0; width:100%; z-index:999; background:#fff; border:1px solid #ccc; display:none;"></div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ajax-search');
    const resultsBox = document.getElementById('search-results');

    searchInput.addEventListener('keyup', function() {
        const query = this.value.trim();

        if (query.length < 2) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
            return;
        }

        fetch(`/ajax/search?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                let html = '';

                if (!data || data.length === 0) {
                    html = '<div style="padding:10px;">No events found.</div>';
                } else {
                    data.forEach(event => {
                        html += `
                            <div style="padding:10px; border-bottom:1px solid #ddd;">
                                <strong>${event.eventname}</strong><br>
                                <small>${event.description.substring(0, 50)}...</small><br>
                                <a href="/events/${event.eventid}" style="color:#6a0dad;">View</a>
                            </div>
                        `;
                    });
                }

                resultsBox.innerHTML = html;
                resultsBox.style.display = 'block';
            });
    });

    // Enter vai para a página de search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            const q = this.value.trim();
            if(q.length > 0) {
                window.location.href = `/events/search?query=${encodeURIComponent(q)}`;
            }
        }
    });
});
</script>
@endpush



