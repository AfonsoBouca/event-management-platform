@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Event Details Card --}}
        <div class="card shadow mb-4">
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
                                @if($event->eventstatus !== 'Cancelled')
                                    <form action="{{ route('events.cancel', $event->eventid) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this event? All participants will be notified.');">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">Cancel Event</button>
                                    </form>
                                @endif
                                <form action="{{ route('events.deleteEvent', $event->eventid) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        @elseif($isAttendee)
                            <form action="{{ route('events.leave', $event->eventid) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this event?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Leave Event</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
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

                @auth 
                    @if($isAttendee)
                        <div class="mt-3">
                            <button id="viewParticipants" class="btn btn-outline-purple" data-bs-toggle="offcanvas" data-bs-target="#participantSidebar">View Participants</button>
                        </div>
                    @endif
                    @if($isAttendee || $event->createdby === Auth::user()->userid)
                        <button id="openMessages"
                            class="btn btn-outline-purple mt-2"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#messagesSidebar">
                            Event Messages
                        </button>
                    @endif

                @endauth

                <div class="bg-light p-3 rounded border-start border-4 border-purple mt-3">
                    <h6 class="text-purple mb-2">Description</h6>
                    <p class="mb-0">{{ $event->description }}</p>
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="d-flex flex-wrap gap-2">
                    @auth
                        {{-- Participation status - always first on the left --}}
                        @if($isAttendee)
                            <span class="btn btn-success disabled">You are participating</span>
                            
                            {{-- Organizer controls (before Photos) --}}
                            @if($event->createdby === Auth::user()->userid)
                                <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-outline-purple">Manage Participation</a>
                            @endif
                            
                            <button id="openPhotos" class="btn btn-outline-purple" data-bs-toggle="offcanvas" data-bs-target="#photosSidebar">Photos</button>
                        @elseif($hasPendingRequest)
                            <span class="btn btn-info disabled">Request Pending</span>
                        @elseif($event->createdby !== Auth::user()->userid && $event->eventstatus !== 'Cancelled')
                            <form action="{{ route('events.join', $event->eventid) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-purple">Request to Join</button>
                            </form>
                        @endif

                        {{-- Polls link - visible to organizer and attendees --}}
                        @if($event->createdby === Auth::user()->userid || $isAttendee)
                            <a href="{{ route('polls.index', $event->eventid) }}" class="btn btn-outline-purple">Polls</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-purple">Login to Join</a>
                    @endauth

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-auto">← Back to Homepage</a>
                </div>
            </div>
        </div>

        {{-- Comments Section - Separate card below event details --}}
        @can('viewComments', $event)
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="text-purple mb-4">Comments</h5>
                    
                    {{-- New Comment Form --}}
                    <div class="card border-0 shadow-sm mb-4 p-3 rounded-3 bg-light">
                        <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
                            @csrf
                            <div class="d-flex gap-3">
                                <img src="{{ Auth::user()->getProfileImage() ?? '/images/default.png' }}" 
                                     class="rounded-circle border" width="45" height="45" alt="Profile">
                                <div class="flex-grow-1">
                                    <textarea 
                                        name="content"
                                        class="form-control rounded-3"
                                        placeholder="Write a comment..."
                                        rows="2"
                                        required
                                    ></textarea>
                                    <button type="submit" class="btn btn-purple btn-sm mt-2">Comment</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Comments List --}}
                    @forelse ($event->comments as $comment)
                        <div class="d-flex mb-4" x-data="{ open: false }">
                            <img src="{{ $comment->author->getprofileImage() ?? '/images/default.png' }}" 
                                 width="45" height="45"
                                 class="rounded-circle me-3 border shadow-sm flex-shrink-0" alt="Profile">
                            <div class="card border-0 shadow-sm p-3 rounded-3 w-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-dark">{{ $comment->author->username }}</strong>
                                        <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($comment->createdat)->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                <p class="mb-2">{{ $comment->content }}</p>
                                
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <form method="POST" action="{{ route('comments.vote', $comment) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            &#x2B06; {{ $comment->voters()->count() }}
                                        </button>
                                    </form>
                                    
                                    @include('comments.comments_actions', ['comment' => $comment])
                                    
                                    <button class="btn btn-link text-purple p-0 ms-2" @click="open = !open">
                                        Reply
                                    </button>
                                </div>

                                {{-- Reply Form --}}
                                <div x-show="open" x-transition class="mt-3 ps-3 border-start border-2">
                                    <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
                                        @csrf
                                        <input type="hidden" name="parentid" value="{{ $comment->commentid }}">
                                        <div class="d-flex gap-2">
                                            <img src="{{ Auth::user()->getProfileImage() ?? '/images/default.png' }}" 
                                                 width="35" height="35"
                                                 class="rounded-circle border shadow-sm flex-shrink-0" alt="Profile">
                                            <div class="flex-grow-1">
                                                <textarea 
                                                    name="content" 
                                                    class="form-control rounded-3"
                                                    rows="2"
                                                    placeholder="Write a reply..."
                                                    required
                                                ></textarea>
                                                <button type="submit" class="btn btn-sm btn-purple mt-2">Reply</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                {{-- Replies --}}
                                @if ($comment->replies->count())
                                    <div class="mt-3 ps-3 border-start border-2">
                                        @foreach ($comment->replies as $reply)
                                            <div class="d-flex mb-3">
                                                <img src="{{ $reply->author->getprofileImage() ?? '/images/default.png' }}" 
                                                     width="35" height="35" 
                                                     class="rounded-circle me-2 border shadow-sm flex-shrink-0" alt="Profile">
                                                <div class="card border-0 shadow-sm p-3 rounded-3 w-100 bg-light">
                                                    <div class="mb-1">
                                                        <strong>{{ $reply->author->username }}</strong>
                                                        <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($reply->createdat)->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                    <p class="mb-1">{{ $reply->content }}</p>
                                                    @include('comments.comments_actions', ['comment' => $reply])
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No comments yet. Be the first to comment!</p>
                    @endforelse
                </div>
            </div>
        @endcan
    </div>
</div>

{{-- Photos Sidebar --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="photosSidebar" style="width:700px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Photos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="photosContent">
            <!-- AJAX loads here -->
        </div>
    </div>
</div>

{{-- Participants Sidebar --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="participantSidebar" style="width:700px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Participants</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="participantsContent">
            <!-- AJAX loads here -->
        </div>
    </div>
</div>

{{-- Event Messages Sidebar --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="messagesSidebar" style="width:700px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Event Messages</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="messagesContent"></div>
    </div>
</div>


{{-- Alpine.js for interactive components --}}
<script src="https://unpkg.com/alpinejs" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openPhotosBtn = document.getElementById('openPhotos');
    if (openPhotosBtn) {
        openPhotosBtn.addEventListener('click', loadPhotos);
    }

    const viewParticipantsBtn = document.getElementById('viewParticipants');
    if (viewParticipantsBtn) {
        viewParticipantsBtn.addEventListener('click', loadParticipants);
    }

    const openMessagesBtn = document.getElementById('openMessages');
    if (openMessagesBtn) {
        openMessagesBtn.addEventListener('click', loadMessages);
    }
});

function loadPhotos() {
    fetch("/events/{{ $event->eventid }}/photos")
        .then(res => res.text())
        .then(html => {
            document.getElementById('photosContent').innerHTML = html;
            attachPhotoEvents();
        });
}

function attachPhotoEvents() {
    const form = document.getElementById('photoUploadForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let data = new FormData(form);

        fetch("/events/{{ $event->eventid }}/photos/upload", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: data
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                loadPhotos();
            }
        });
    });
}

function loadParticipants() {
    fetch("/events/{{ $event->eventid }}/viewParticipants")
        .then(res => res.text())
        .then(html => {
            document.getElementById('participantsContent').innerHTML = html;
        });
}


function loadMessages() {
    fetch("/events/{{ $event->eventid }}/messages")
        .then(res => res.text())
        .then(html => {
            document.getElementById('messagesContent').innerHTML = html;
            attachMessageForm();
        });
}

function attachMessageForm() {
    const form = document.getElementById('messageForm');
    if (!form) return;

    form.addEventListener('submit', e => {
        e.preventDefault();

        fetch("/events/{{ $event->eventid }}/messages", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: new FormData(form)
        })
        .then(() => loadMessages());
    });
}

</script>

@endsection