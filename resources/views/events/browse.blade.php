@extends('layouts.app')

@section('content')

<div class="container">
    <h1>Browse Events</h1>

    @if($events->count() == 0)
        <div class ="alert alert-info">No events found.</div>
    @endif

    <div id="events-list-home">
    @foreach($events as $event)
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-purple">{{ $event->eventname }}</h5>
            <p class="card-text text-muted">{{ Str::limit($event->description,80) }}</p>
            <p class="card-text">
                <small class="text-muted">Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</small><br>
                </p>
                </div>
                <div class="card-footer bg-transparent">
            <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-purple btn-sm">View Event</a>
        </div>
        </div>
        </div>
    @endforeach
</div>

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
