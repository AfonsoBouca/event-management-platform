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
           <a class="view-event-btn filter-btn" data-status="upcoming">Upcoming</a>
           <a class="view-event-btn filter-btn" data-status="ongoing">Ongoing</a>
           <a class="view-event-btn filter-btn" data-status="completed">Completed</a>
           <a class="view-event-btn filter-btn" data-status="cancelled">Cancelled</a>

    <!-- Categories -->
            <a class="view-event-btn filter-btn" data-category="Music">Music</a>
            <a class="view-event-btn filter-btn" data-category="Tech">Technology</a>
            <a class="view-event-btn filter-btn" data-category="Sports">Sports</a>
            <a class="view-event-btn filter-btn" data-category="Business">Business</a>
            <a class="view-event-btn filter-btn" data-category="Arts & Culture">Arts & Culture</a>
            <a class="view-event-btn filter-btn" data-category="Education">Education</a>
            <a class="view-event-btn filter-btn" data-category="Health & Wellness">Health & Wellness</a>
            <a class="view-event-btn filter-btn" data-category="Food & Drink">Food & Drink</a>
            <a class="view-event-btn filter-btn" data-category="Networking">Networking</a>
            <a class="view-event-btn filter-btn" data-category="Charity">Charity</a>
</div>

  <div id="events-list-home">
    @foreach ($upcomingEvents->take(5) as $event)
        <div class="event-card">
            <strong>{{ $event->eventname }}</strong>
            <p>{{ Str::limit($event->description, 80) }}</p>
            <span>Starts: {{ $event->startdate->format('d/m/Y H:i') }}</span><br>
            <a class="view-event-btn" href="{{ route('events.show', ['id' => $event->eventid]) }}">View Event</a>
      </div>
    @endforeach
  </div>
  <div id="events-pagination-home"> </div>
    </section>

    <!-- Recently Added -->
    <section class="event-section" style="margin-top:30px;">
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

// ---AJAX Browser filters -----
document.addEventListener('DOMContentLoaded', function() {

const eventsListHome = document.getElementById('events-list-home');
const eventsPaginationHome = document.getElementById('events-pagination-home');

let currentStatus = '';
let currentCategory = '';

function fetchEventsHome(page = 1) {
    const params = new URLSearchParams();
    if (currentStatus) params.append('status', currentStatus);
    if (currentCategory) params.append('category', currentCategory);
    params.append('page', page);

    fetch(`/events/browse?${params.toString()}`)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Atualiza lista e paginação
            eventsListHome.innerHTML = doc.getElementById('events-list-home').innerHTML;
            eventsPaginationHome.innerHTML = doc.getElementById('events-pagination-home').innerHTML;

            attachPaginationEventsHome();
        });
}

function attachPaginationEventsHome() {
    document.querySelectorAll('#events-pagination-home .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.dataset.page || this.getAttribute('href').split('page=')[1];
            fetchEventsHome(page);
        });
    });
}

// Filtros
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        currentStatus = this.dataset.status || '';
        currentCategory = this.dataset.category || '';

        fetchEventsHome(1); // volta para página 1
    });
});

});
</script>
@endpush

