@extends('layouts.app')

@section('content')
<style>
    .event-container {
        max-width: 900px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #000;
    }

    .event-header {
        border-bottom: 2px solid #dda0dd;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .event-title {
        font-size: 2em;
        color: #6a0dad;
        margin-bottom: 10px;
    }

    .event-status {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-upcoming { background-color: #4caf50; color: white; }
    .status-ongoing { background-color: #ff9800; color: white; }
    .status-completed { background-color: #9e9e9e; color: white; }
    .status-cancelled { background-color: #f44336; color: white; }

    .event-details {
        margin: 20px 0;
    }

    .detail-row {
        display: flex;
        margin: 12px 0;
        font-size: 1em;
    }

    .detail-label {
        font-weight: bold;
        color: #6a0dad;
        min-width: 150px;
    }

    .detail-value {
        color: #333;
    }

    .event-description {
        margin-top: 25px;
        padding: 20px;
        background-color: #f9f9f9;
        border-left: 4px solid #6a0dad;
        border-radius: 5px;
    }

    .event-description h3 {
        color: #6a0dad;
        margin-bottom: 10px;
    }

    .action-buttons {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .btn-secondary {
        background-color: #9e9e9e;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #757575;
    }

    .btn-success {
        background-color: #4caf50;
        color: white;
    }

    .btn-info {
        background-color: #2196f3;
        color: white;
    }

    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .visibility-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.85em;
        margin-left: 10px;
    }

    .badge-public {
        background-color: #4caf50;
        color: white;
    }

    .badge-private {
        background-color: #f44336;
        color: white;
    }
</style>

<div class="event-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="event-header">
        <h1 class="event-title">
            {{ $event->eventname }}
            <span class="visibility-badge {{ $event->ispublic ? 'badge-public' : 'badge-private' }}">
                {{ $event->ispublic ? 'Public' : 'Private' }}
            </span>
        </h1>
        <span class="event-status status-{{ strtolower($event->eventstatus) }}">
            {{ $event->eventstatus }}
        </span>
    </div>

    <div class="event-details">
        <div class="detail-row">
            <span class="detail-label">Location:</span>
            <span class="detail-value">{{ $event->location }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Start Date:</span>
            <span class="detail-value">{{ $event->startdate->format('d/m/Y H:i') }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">End Date:</span>
            <span class="detail-value">{{ $event->enddate->format('d/m/Y H:i') }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Capacity:</span>
            <span class="detail-value">{{ $event->capacity }} people</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Price:</span>
            <span class="detail-value">{{ $event->price > 0 ? '€' . number_format($event->price, 2) : 'Free' }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Organizer:</span>
            <span class="detail-value">
                <a href="{{ route('user.profile', $event->organizer->username) }}" style="color: #6a0dad; text-decoration: none;">
                    {{ $event->organizer->username }}
                </a>
            </span>
        </div>
    </div>

    <div class="event-description">
        <h3>Description</h3>
        <p>{{ $event->description }}</p>
    </div>

    <div class="action-buttons">
        @auth
            @if(Auth::check() && $event->createdby === Auth::user()->userid)
                <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-primary">
                    Manage Participation
                </a>
            @endif

            @if($isAttendee)
                <span class="btn btn-success">You are participating</span>
            @elseif($hasPendingRequest)
                <span class="btn btn-info">Request Pending</span>
            @else
                <form action="{{ route('events.join', $event->eventid) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Request to Join</button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login to Join</a>
        @endauth

        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>
@endsection