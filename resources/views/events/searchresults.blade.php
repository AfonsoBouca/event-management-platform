@extends('layouts.app')

@section('content')
<style>
    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        color: #fff;
    }

    h2 {
        text-align: auto;
        color: #dda0dd; 
        margin-bottom: 20px;
    }

    p {
        text-align: auto;
        color: #000;
        font-size: 0.95em;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    li {
        background-color:rgb(255, 255, 255);
        border: 1px solid #444;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    li:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgb(255, 255, 255);
    }

    li strong {
        font-size: 1.1em;
        color: #dda0dd; /* roxo suave */
        display: block;
        margin-bottom: 5px;
    }

    li p, li span {
        color: #000;
        margin: 5px 0;
        font-size: 0.95em;
    }

    .back-btn {
        display: inline-block;
        margin: 20px auto;
        padding: 8px 15px;
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgb(255, 255, 255);
    }
</style>

<div class="container">
    <h2>Results for: "{{ $query }}"</h2>

    @if($events->isEmpty())
        <p>No events found.</p>
    @else
        <ul>
        @foreach($events as $event)
        <div class="event-card">
            <strong>{{ $event->eventname }}</strong>
            <p>{{ $event->description }}</p>
            <span>Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span><br>
            <a href="{{ route('events.show', $event->eventid) }}" class="view-event-btn">View Event</a>
        </div>
    @endforeach
        </ul>
    @endif

    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="back-btn">Back to Homepage</a>
    </div>
</div>
@endsection
