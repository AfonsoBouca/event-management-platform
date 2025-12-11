@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h4 class="text-purple mb-0">{{ $poll->title }}</h4>
                <small class="text-muted">Event: <a href="{{ route('events.show', $event->eventid) }}">{{ $event->eventname }}</a></small>
            </div>
            
            <div class="card-body">
                <p class="text-muted">{{ $poll->content }}</p>
                
                <hr>
                
                <h6 class="text-purple mb-3">Results</h6>
                <p class="text-muted">Total votes: <strong>{{ $results['totalVotes'] }}</strong></p>
                
                @foreach($results['options'] as $option)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium">{{ $option['text'] }}</span>
                            <span class="text-muted">{{ $option['voteCount'] }} votes ({{ $option['percentage'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-purple" role="progressbar" 
                                 style="width: {{ $option['percentage'] }}%;" 
                                 aria-valuenow="{{ $option['percentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $option['percentage'] }}%
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="card-footer bg-white">
                <a href="{{ route('polls.index', $event->eventid) }}" class="btn btn-outline-secondary">
                    &larr; Back to Polls
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
