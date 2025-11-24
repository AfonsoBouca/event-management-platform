@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Participation - {{ $event->eventname }}</h1>
    
    <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-secondary mb-3">
        ← Back to Event
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!$event->ispublic)
        <h2>Invite Participants</h2>

        <a href="{{ route('events.invite.search', $event->eventid) }}" class="btn btn-primary">
            Add Participant
        </a>

        <h2> Invited Participants </h2>
            @if($pendingInvitations->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Invite Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingInvitations as $inv)
                            <tr>
                                <td>{{ $inv->user->username }}</td>
                                <td>{{ $inv->user->email }}</td>
                                <td>
                                    <span class="badge bg-warning">Invitation Sent</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No pending invitations.</p>
            @endif        

    @endif

    <h2>Pending Join Requests</h2>   
    @if($pendingRequests->count() > 0)
        <table class="table">
            <thead>
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
                            <form action="{{ route('events.join-requests.approve', [$event->eventid, $request->joinid]) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">✓ Approve</button>
                            </form>
                            
                            <form action="{{ route('events.join-requests.reject', [$event->eventid, $request->joinid]) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">✗ Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No pending requests.</p>
    @endif

    <hr class="my-5">

    <h2>Current Participants ({{ $participants->count() }})</h2>
    
    <table class="table">
        <thead>
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
                            <span class="badge bg-primary">Organizer</span>
                        @else
                            <span class="badge bg-secondary">Attendee</span>
                        @endif
                    </td>
                    <td>
                        @if($participant->pivot->role !== 'Organizer')
                            <form action="{{ route('events.participants.remove', [$event->eventid, $participant->userid]) }}" method="POST" onsubmit="return confirm('Remove this participant?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
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
@endsection