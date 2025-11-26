@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-purple mb-4">Browse Events</h1>

    @if($events->count() == 0)
        <div class="alert alert-info">No events found.</div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($events as $event)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-purple">{{ $event->eventname }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-purple btn-sm">View Event</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginacao Bootstrap -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                @if ($events->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $events->previousPageUrl() }}">&laquo; Prev</a></li>
                @endif

                @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                    <li class="page-item {{ $events->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                @if ($events->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $events->nextPageUrl() }}">Next &raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
                @endif
            </ul>
        </nav>
    @endif
</div>
@endsection
