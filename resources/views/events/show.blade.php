@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3 class="text-purple mb-1">{{ $event->eventname }}</h3>
                        <span class="badge {{ $event->ispublic ? 'bg-success' : 'bg-danger' }}">
                            {{ $event->ispublic ? 'Public' : 'Private' }}
                        </span>
                        <span class="badge badge-{{ strtolower($event->eventstatus) }}">
                            {{ $event->eventstatus }}
                        </span>
                    </div>
                    
                    @auth
                        @if($event->createdby === Auth::user()->userid)
                            <div class="d-flex gap-2">
                                <a href="{{ route('events.editEvent', $event->eventid) }}" class="btn btn-purple btn-sm">Edit</a>
                                <form action="{{ route('events.deleteEvent', $event->eventid) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-purple" style="width: 150px;">Location</th>
                        <td>{{ $event->location }}</td>
                    </tr>
                    <tr>
                        <th class="text-purple">Start Date</th>
                        <td>{{ $event->startdate->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-purple">End Date</th>
                        <td>{{ $event->enddate->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-purple">Capacity</th>
                        <td>{{ $event->capacity }} people</td>
                    </tr>
                    <tr>
                        <th class="text-purple">Price</th>
                        <td>{{ $event->price > 0 ? '€' . number_format($event->price, 2) : 'Free' }}</td>
                    </tr>
                    <tr>
                        <th class="text-purple">Organizer</th>
                        <td><a href="{{ route('user.profile', $event->organizer->username) }}">{{ $event->organizer->username }}</a></td>
                    </tr>
                </table>

                <div class="bg-light p-3 rounded border-start border-4 border-purple mt-3">
                    <h6 class="text-purple">Description</h6>
                    <p class="mb-0">{{ $event->description }}</p>
                </div>
            </div>
@can('viewComments', $event)
 <div class="card mb-3">
  <div class="card-body">
   <h5 class="text-purple mb-3">Comments</h5>
    <div class="card shadow-sm border-0 mb-4 p-3 rounded-4">
        <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
            @csrf

            <div class="d-flex mb-3">
                <img src="{{ Auth::user()->profilepicture ?? '/images/default.png' }}" 
                     class="rounded-circle me-2 border" width="45" height="45">

                <textarea 
                    name="content"
                    class="form-control rounded-3 shadow-sm"
                    placeholder="Write a comment..."
                    rows="2"
                    required
                ></textarea>
            </div>

            <button type="submit" class="btn btn-purple">Comment</button>
        </form>
    </div>

  
    @foreach ($event->comments as $comment)
        <div class="d-flex mb-4" x-data="{ open: false, openEdit:false }">
            <img src="{{ $comment->author->profilepicture ?? '/profile/default.png' }}" 
                width="45" height="45"
                class="rounded-circle me-3 border shadow-sm">
            <div class="card border-0 shadow-sm p-3 rounded-4 w-100">
              <strong class="text-dark">{{ $comment->author->username }}</strong>
              <small class="text-muted">{{ \Carbon\Carbon::parse($comment->createdat)->format('d/m/Y H:i') }}</small>
    
              <p class="mb-2">{{ $comment->content }}</p>
     <div class="d-flex align-items-center mb-2">
       <form method="POST" action="{{ route('comments.vote', $comment) }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary me-2">
            &#x2B06; 
        </button>
    </form>
    <span>{{ $comment->voters()->count() }}</span>
</div>


              @include('comments.comments_actions', ['comment' => $comment])
                <button class="btn btn-link text-purple p-0"
                        @click="open = !open">
                    Reply
                </button>
                <div x-show="open" x-transition class="mt-3">
                    <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
                        @csrf

                        <input type="hidden" name="parentid" value="{{ $comment->commentid }}">

                        <div class="d-flex mb-2">
                            <img src="{{ Auth::user()->profilepicture ?? '/images/default.png' }}" 
                                width="35" height="35"
                                class="rounded-circle me-2 border shadow-sm">

                            <textarea 
                                name="content" 
                                class="form-control rounded-3 shadow-sm"
                                rows="2"
                                placeholder="Write a reply..."
                                required
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-purple">Reply</button>
                    </form>
                </div>
                @if ($comment->replies->count())
                    <div class="mt-3 ps-4 border-start">

                        @foreach ($comment->replies as $reply)
                            <div class="d-flex mb-3">

                                <img src="{{ $reply->author->profilepicture ?? '/images/default.png' }}" 
                                     width="35" height="35" 
                                     class="rounded-circle me-2 border shadow-sm">

                                <div class="card border-0 shadow-sm p-3 rounded-4 w-100">
                                    <strong>{{ $reply->author->username }}</strong>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reply->createdat)->format('d/m/Y H:i') }}</small>
                                      <p class="mb-1">{{ $reply->content }}</p>
                                      @include('comments.comments_actions', ['comment' => $reply])
                                </div>

                            </div>
                        @endforeach

                    </div>
                @endif
            </div>
        </div>
    @endforeach

<script src="https://unpkg.com/alpinejs" defer></script>

<script>
document.addEventListener('alpine:init', () => {
    @foreach ($event->comments as $comment)
        Alpine.data('replyComponent{{ $comment->commentid }}', () => ({
            reply{{ $comment->commentid }}: false,
        }))
    @endforeach
});
</script>
@endcan

             <div class="card-footer bg-white">
                <div class="d-flex flex-wrap gap-2">
                    @auth
                        @if(Auth::check() && $event->createdby === Auth::user()->userid)
                            <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-purple">Manage Participation</a>
                        @endif

                        {{-- Polls link - visible to organizer and attendees --}}
                        @if($event->createdby === Auth::user()->userid || $isAttendee)
                            <a href="{{ route('polls.index', $event->eventid) }}" class="btn btn-outline-purple">Polls</a>
                        @endif

                        @if($isAttendee)
                            <span class="btn btn-success disabled">You are participating</span>
                        @elseif($hasPendingRequest)
                            <span class="btn btn-info disabled">Request Pending</span>
                        @else
                            <form action="{{ route('events.join', $event->eventid) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-purple">Request to Join</button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-purple">Login to Join</a>
                    @endauth

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">← Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection