@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Browse Events</h1>

    @if($events->count() == 0)
        <p>No events found.</p>
    @endif

    @foreach($events as $event)
        <div class="event-card">
            <strong>{{ $event->eventname }}</strong>
            <p>{{ $event->description }}</p>
            <span>Starts: {{ $event->startdate->format('d/m/Y H:i') }}</span><br>
            <a href="{{ route('events.show', $event->eventid) }}" class="view-event-btn">View Event</a>
        </div>
    @endforeach

    <div class="mt-3">
        {{ $events->withQueryString()->links() }}
    </div>
</div>
@endsection
