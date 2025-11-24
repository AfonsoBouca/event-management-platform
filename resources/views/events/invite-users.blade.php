@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Invite Participants – {{ $event->eventname }}</h1>

    <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-secondary mb-3">
        ← Back to Manage
    </a>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('events.searchUsers', $event->eventid) }}">
        <div class="input-group mb-4">
            <input type="text" name="q" placeholder="Search by username or email"
                   value="{{ $query }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    {{-- Search results --}}

    @if($users && count($users) > 0)
        <h4>Results:</h4>
        <ul class="list-group">
            @foreach($users as $u)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $u->username }}</strong><br>
                        <small>{{ $u->email }}</small>
                    </div>

                    <form method="POST"
                          action="{{ route('events.inviteUser', ['eventid' => $event->eventid, 'userid' => $u->userid]) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">Invite</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @elseif($query)
        <p class="text-muted">No users found.</p>
    @endif
</div>
@endsection
