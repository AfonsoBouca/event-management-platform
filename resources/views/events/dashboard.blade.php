@extends('layouts.app')

@section('content')
<!-- Search Bar -->
<div class="row mb-4">
    <div class="col-md-6 position-relative">
        <input id="ajax-search" type="text" class="form-control" placeholder="Search Events..." autocomplete="off">
        <div id="search-results" class="position-absolute w-100 bg-white border rounded shadow-sm" style="z-index:999; display:none; top:100%;"></div>
    </div>
</div>

<!-- Browse by Status -->
<section class="mb-4">
    <h5 class="text-purple mb-3">Browse by Status</h5>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-sm btn-outline-success filter-btn" data-status="upcoming">Upcoming</a>
        <a class="btn btn-sm btn-outline-warning filter-btn" data-status="ongoing">Ongoing</a>
        <a class="btn btn-sm btn-outline-secondary filter-btn" data-status="completed">Completed</a>
        <a class="btn btn-sm btn-outline-danger filter-btn" data-status="cancelled">Cancelled</a>
    </div>
</section>

<!-- Browse by Category -->
<section class="mb-4">
    <h5 class="text-purple mb-3">Browse by Category</h5>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Music">Music</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Technology">Technology</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Sports">Sports</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Business">Business</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Arts & Culture">Arts & Culture</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Education">Education</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Health & Wellness">Health & Wellness</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Food & Drink">Food & Drink</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Networking">Networking</a>
        <a class="btn btn-sm btn-outline-purple filter-btn" data-category="Charity">Charity</a>
    </div>
</section>

<!-- Upcoming Events -->
<section class="mb-5">
    <div id="events-list-home" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        @foreach($upcomingEvents->take(6) as $event)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title text-purple">{{ $event->eventname }}</h6>
                        <p class="card-text small text-muted">{{ Str::limit($event->description, 80) }}</p>
                        <p class="card-text"><small class="text-muted">{{ $event->startdate->format('d/m/Y H:i') }}</small></p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a class="btn btn-purple btn-sm" href="{{ route('events.show', ['id' => $event->eventid]) }}">View Event</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div id="events-pagination-home" class="mt-3"></div>
</section>

<!-- Recently Added -->
<section class="mb-4">
    <h4 class="text-purple border-bottom pb-2 mb-3">Recently Added</h4>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        @foreach($recentEvents->take(6) as $event)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title text-purple">{{ $event->eventname }}</h6>
                        <p class="card-text small text-muted">{{ Str::limit($event->description, 80) }}</p>
                        <p class="card-text"><small class="text-muted">{{ $event->startdate->format('d/m/Y H:i') }}</small></p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a class="btn btn-purple btn-sm" href="{{ route('events.show', ['id' => $event->eventid]) }}">View Event</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
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
                    html = '<div class="p-3">No events found.</div>';
                } else {
                    data.forEach(event => {
                        html += `
                            <div class="p-2 border-bottom">
                                <strong>${event.eventname}</strong><br>
                                <small class="text-muted">${event.description.substring(0, 50)}...</small><br>
                                <a href="/events/${event.eventid}" class="text-purple">View</a>
                            </div>
                        `;
                    });
                }

                resultsBox.innerHTML = html;
                resultsBox.style.display = 'block';
            });
    });

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

// AJAX Browser filters
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
                
                const newList = doc.getElementById('events-list-home');
                const newPagination = doc.getElementById('events-pagination-home');
                
                if (newList) eventsListHome.innerHTML = newList.innerHTML;
                if (newPagination) eventsPaginationHome.innerHTML = newPagination.innerHTML;

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

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentStatus = this.dataset.status || '';
            currentCategory = this.dataset.category || '';
            fetchEventsHome(1);
        });
    });
});
</script>
@endpush
