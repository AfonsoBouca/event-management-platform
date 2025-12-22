@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-purple mb-1">Polls</h3>
                <p class="text-muted mb-0">Event: <a href="{{ route('events.show', $event->eventid) }}">{{ $event->eventname }}</a></p>
            </div>
            
            @if($event->isOrganizer(Auth::user()->userid))
                <a href="{{ route('polls.create', $event->eventid) }}" class="btn btn-purple">
                    <i class="bi bi-plus-lg"></i> Create Poll
                </a>
            @endif
        </div>

        <!-- Polls List -->
        @forelse($polls as $poll)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-purple">{{ $poll->title }}</h5>
                        <small class="text-muted">Created {{ $poll->createdat->diffForHumans() }}</small>
                    </div>
                    
                    @if($event->isOrganizer(Auth::user()->userid))
                        <form action="{{ route('polls.destroy', $poll->pollid) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this poll?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                        </form>
                    @endif
                </div>
                
                <div class="card-body">
                    <p class="text-muted">{{ $poll->content }}</p>
                    @php
    $totalVotes = $poll->total_votes;
    $userVote = null;

    if (!$showResultsOnly) {
        $hasVoted = $poll->hasUserVoted(Auth::user()->userid);
        $userVote = $hasVoted ? $poll->getUserVote(Auth::user()->userid) : null;
    }
@endphp

@if($showResultsOnly || ($userVote))
    <!-- Mostra apenas resultados -->
    <div class="mt-3">
        <small class="text-muted d-block mb-2">Total votes: {{ $totalVotes }}</small>

        @foreach($poll->options as $option)
            @php
                $voteCount = $option->vote_count;
                $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;
                $isUserVote = $userVote && $userVote->polloptionid === $option->polloptionid;
            @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="{{ $isUserVote ? 'fw-bold text-purple' : '' }}">
                        {{ $option->optiontext }}
                        @if($isUserVote)
                            <span class="badge bg-purple">Your vote</span>
                        @endif
                    </span>
                    <span class="text-muted">{{ $voteCount }} votes ({{ $percentage }}%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-purple" style="width: {{ $percentage }}%;"></div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Formulário de votação para participantes -->
    <form action="{{ route('polls.vote', $poll->pollid) }}" method="POST">
        @csrf
        <div class="mt-3">
            @foreach($poll->options as $option)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" 
                           name="option_id" 
                           id="option_{{ $option->polloptionid }}" 
                           value="{{ $option->polloptionid }}" required>
                    <label class="form-check-label" for="option_{{ $option->polloptionid }}">
                        {{ $option->optiontext }}
                    </label>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-purple mt-3">Vote</button>
    </form>
@endif
                </div>
            </div>
        @empty
            <p class="text-muted">No polls have been created for this event yet.</p>
        @endforelse    
                   
        <!-- Back Button -->
        <div class="mt-4">
            <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-outline-secondary">
                &larr; Back to Event
            </a>
        </div>
    </div>
</div>
@endsection
