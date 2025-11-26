@extends('layouts.app')

@section('content')
<style>
    /* --- Container --- */
    .container {
        margin: 20px auto;
        max-width: 900px;
        padding: 0 15px;
    }

    /* --- Título --- */
    h1 {
        color:rgb(0, 0, 0); 
        margin-bottom: 20px;
    }

    /* --- Cartões de evento --- */
    .event-card {
        border: 1px solid #444;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color:rgb(255, 255, 255); 
        color: rgb(0, 0, 0);
    }

    .event-card strong {
        font-size: 1.1em;
        display: block;
        margin-bottom: 5px;
        color: #dda0dd; 
    }

    .event-card p {
        margin: 5px 0;
    }

    .event-card span {
        font-size: 0.85em;
        color: #ccc;
    }

    .view-event-btn {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 10px;
        background-color: #6a0dad; 
        color: #fff;
        text-decoration: none;
        border-radius: 3px;
        font-size: 0.85em;
    }

    .view-event-btn:hover {
        background-color: #7d1dcf; /* roxo claro */
    }

    /* --- Paginação customizada --- */
    .pagination-container {
        margin-top: 20px;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-container .page-link {
        padding: 5px 10px;
        color: #6a0dad; 
        border: 1px solid #6a0dad;
        border-radius: 3px;
        font-size: 0.85em;
        text-decoration: none;
    }

    .pagination-container .page-link:hover {
        background-color: #6a0dad;
        color: #fff;
    }

    .pagination-container .active {
        background-color: #6a0dad;
        color: #fff;
        border-color: #6a0dad;
    }
</style>

@section('content')
<div class="container">
    <h1>Browse Events</h1>

    @if($events->count() == 0)
        <p>No events found.</p>
    @endif

    <div id="events-list-home">
    @foreach($events as $event)
        <div class="event-card">
            <strong>{{ $event->eventname }}</strong>
            <p>{{ Str::limit($event->description,80) }}</p>
            <span>Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span><br>
            <a href="{{ route('events.show', $event->eventid) }}" class="view-event-btn">View Event</a>
        </div>
    @endforeach
</div>

    <!-- Paginação -->
   <div id="events-pagination-home">  
    <div class="pagination-container">
        @if ($events->onFirstPage() === false)
            <a href="{{ $events->previousPageUrl() }}" class="page-link">&laquo; Prev</a>
        @endif

        @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $events->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if ($events->hasMorePages())
            <a href="{{ $events->nextPageUrl() }}" class="page-link">Next &raquo;</a>
        @endif
    </div>
</div>
</div>
@endsection
