@foreach($events as $event)
    <div class="event-card">
        <strong>{{ $event->eventname }}</strong>
        <p>{{ Str::limit($event->description, 80) }}</p>
        <span>Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span><br>
        <a href="{{ route('events.show', $event->eventid) }}" class="view-event-btn">View Event</a>
    </div>
@endforeach

@if($events->count() == 0)
    <p>No events found.</p>
@endif
