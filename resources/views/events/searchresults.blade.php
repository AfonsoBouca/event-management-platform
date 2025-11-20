<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Results</title>
    </head>
    <body>
    <h2>Results for: "{{ $query }}"</h2>

@if($events->isEmpty())
    <p>No events found.</p>
@else
    <ul>
    @foreach($events as $event)
        <li>
            <strong>{{ $event->eventname }}</strong><br>
            {{ $event->description }}<br>
            @if($event->startdate)
                Begin: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}
            @else
                Begin: Not defined
            @endif
        </li>
    @endforeach
    </ul>
@endif


    </body>
</html>