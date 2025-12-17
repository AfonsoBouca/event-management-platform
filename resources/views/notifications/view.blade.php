@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Notifications</h2>

    @if($notifications->isEmpty())
        <div class="alert alert-info">
            You have no notifications.
        </div>
    @else
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications as $n)
                    <tr class="{{ $n->isRead ? '' : 'table-warning' }}">
                        <td>{{ $n->title }}</td>
                        <td>{{ $n->message }}</td>
                        <td>{{ \Carbon\Carbon::parse($n->time)->diffForHumans() }}</td>
                        <td>
                            @if($n->isread)
                                <span class="badge bg-success">Read</span>
                            @else
                                <span class="badge bg-secondary">Unread</span>
                            @endif
                        </td>
                        <td>
                            @if(!$n->isRead)
                                <form method="POST" action="{{ route('notifications.read', $n->notificationid) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
