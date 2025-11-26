@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple mb-0">Manage Participation – {{ $event->eventname }}</h2>
        <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-outline-secondary">
            ← Back to Event
        </a>
    </div>

    {{-- Invite Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Invite Participants</h5>
            <a href="{{ route('events.invite.search', $event->eventid) }}" class="btn btn-light btn-sm">
                + Add Participant
            </a>
        </div>
    </div>

    {{-- Pending Invitations --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Invited Participants</h5>
        </div>
        @if($pendingInvitations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingInvitations as $inv)
                            <tr>
                                <td>{{ $inv->user->username }}</td>
                                <td>{{ $inv->user->email }}</td>
                                <td><span class="badge bg-warning text-dark">Invitation Sent</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <p class="text-muted mb-0">No pending invitations.</p>
            </div>
        @endif
    </div>

    {{-- Pending Join Requests --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Pending Join Requests</h5>
        </div>
        @if($pendingRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                            <tr>
                                <td>{{ $request->user->username }}</td>
                                <td>{{ $request->user->email }}</td>
                                <td>
                                    <form action="{{ route('events.join-requests.approve', [$event->eventid, $request->joinid]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">✓ Approve</button>
                                    </form>
                                    <form action="{{ route('events.join-requests.reject', [$event->eventid, $request->joinid]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">✗ Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <p class="text-muted mb-0">No pending requests.</p>
            </div>
        @endif
    </div>

    {{-- Current Participants --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Current Participants ({{ $participants->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr>
                            <td>{{ $participant->username }}</td>
                            <td>{{ $participant->email }}</td>
                            <td>
                                @if($participant->pivot->role === 'Organizer')
                                    <span class="badge bg-purple">Organizer</span>
                                @else
                                    <span class="badge bg-secondary">Attendee</span>
                                @endif
                            </td>
                            <td>
                                @if($participant->pivot->role !== 'Organizer')
                                    <form action="{{ route('events.participants.remove', [$event->eventid, $participant->userid]) }}" method="POST" onsubmit="return confirm('Remove this participant?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection