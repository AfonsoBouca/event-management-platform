@extends('layouts.app')

@section('content')
<!-- Profile Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ asset('storage/' . $user->profileimage) }}" 
                 alt="Profile" 
                 class="rounded-circle border border-3 border-purple"
                 style="width: 100px; height: 100px; object-fit: cover;">
            <div class="flex-grow-1">
                <h3 class="text-purple mb-1">{{ $user->username }}</h3>
                <p class="text-muted mb-0">{{ $user->email }}</p>
            </div>
            @if(Auth::check() && Auth::user()->userid === $user->userid)
                <a href="{{ route('user.edit') }}" class="btn btn-outline-purple">Edit Profile</a>
            @endif
        </div>
    </div>
</div>

<!-- Actions -->
<div class="d-flex gap-2 mb-4">
    @if(Auth::check() && Auth::user()->userid === $user->userid)
        <a href="{{ route('events.create') }}" class="btn btn-purple">Create Event</a>
    @endif
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Homepage</a>
</div>

<!-- My Events -->
<section class="mb-5">
    <h5 class="text-purple border-bottom pb-2 mb-3">My Events</h5>
    <div class="events-carousel">
        @forelse($events as $event)
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-purple">{{ $event->eventname }}</h6>
                    <p class="card-text small text-muted">{{ Str::limit($event->description, 80) }}</p>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-purple btn-sm">View</a>
                </div>
            </div>
        @empty
            <p class="text-muted">You haven't created any events yet.</p>
        @endforelse
    </div>
</section>

<!-- Attending Events -->
<section class="mb-5">
    <h5 class="text-purple border-bottom pb-2 mb-3">Attending Events</h5>
    <div class="events-carousel">
        @forelse($participants as $event)
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-purple">{{ $event->eventname }}</h6>
                    <p class="card-text small text-muted">{{ Str::limit($event->description, 80) }}</p>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-purple btn-sm">View</a>
                </div>
            </div>
        @empty
            <p class="text-muted">You're not attending any events.</p>
        @endforelse
    </div>
</section>

<!-- Invitations -->
@if($pendingInvitations->count() > 0)
<section class="mb-4">
    <h5 class="text-purple border-bottom pb-2 mb-3">Pending Invitations</h5>
    <div class="events-carousel">
        @foreach($pendingInvitations as $inv)
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="card-title text-purple">{{ $inv->event->eventname }}</h6>
                    <p class="card-text small text-muted">{{ Str::limit($inv->event->description, 80) }}</p>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($inv->event->startdate)->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-footer bg-transparent d-flex gap-2">
                    <form action="{{ route('invitation.accept', $inv->invitationid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                    </form>
                    <form action="{{ route('invitation.reject', $inv->invitationid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection

