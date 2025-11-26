@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple mb-0">Invite Participants – {{ $event->eventname }}</h2>
        <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-outline-secondary">
            ← Back to Manage
        </a>
    </div>

    {{-- Search bar --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('events.searchUsers', $event->eventid) }}">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search by username or email"
                           value="{{ $query }}">
                    <button class="btn btn-purple" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Search results --}}
    @if($users && count($users) > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Search Results</h5>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($users as $u)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $u->username }}</strong><br>
                            <small class="text-muted">{{ $u->email }}</small>
                        </div>
                        <form method="POST"
                              action="{{ route('events.inviteUser', ['eventid' => $event->eventid, 'userid' => $u->userid]) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Invite</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif($query)
        <div class="alert alert-info">No users found matching "{{ $query }}".</div>
    @endif
</div>
@endsection
