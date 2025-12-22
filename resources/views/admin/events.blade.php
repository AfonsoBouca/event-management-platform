@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Browse Events - {{ auth()->guard('admin')->user()->username }}</h1>

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or description" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    @if($events->count() == 0)
        <div class="alert alert-info">No events found.</div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($events as $event)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->eventname }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($event->description, 80) }}</p>
                        <p class="card-text">
                            <small class="text-muted">Starts: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</small><br>
                            <small class="text-muted">Ends: {{ \Carbon\Carbon::parse($event->enddate)->format('d/m/Y H:i') }}</small><br>
                            <small class="text-muted">Status: {{ $event->eventstatus }}</small><br>
                            <small class="text-muted">Created by: {{ $event->creator }}</small>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('events.show', $event->eventId) }}" class="btn btn-primary btn-sm">View Event</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection
