@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple mb-0">Results for: "{{ $query }}"</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-purple">← Back to Homepage</a>
    </div>

    @if($events->isEmpty())
        <div class="alert alert-info">
            No events found matching your search.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($events as $event)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-purple">{{ $event->eventname }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-purple btn-sm w-100">View Event</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
