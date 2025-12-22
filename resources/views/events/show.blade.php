@extends('layouts.app')

@section('content')

@php
    $admin = auth('admin')->check();
    $organizer = Auth::check() && $event->createdby === Auth::user()->userid;
    $canSeeExtras = $admin || $organizer || $isAttendee;
@endphp


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
                    
                    
                        @if(($event->createdby === Auth::id()) || $admin)
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

                <div>
                    @auth 
                        @if($isAttendee)
                            <button id="viewParticipants" class="btn btn-outline-purple " data-bs-toggle="offcanvas" data-bs-target="#participantSidebar"> View Participants </button>
                        @endif
                    @endauth
                </div>

                <div class="bg-light p-3 rounded border-start border-4 border-purple mt-3">
                    <h6 class="text-purple">Description</h6>
                    <p class="mb-0">{{ $event->description }}</p>
                </div>
            </div>
@can('viewComments', $event)
 <div class="card mb-3">
  <div class="card-body">
   <h5 class="text-purple mb-3">Comments</h5>
   @if(!auth('admin')->check())
    <div class="card shadow-sm border-0 mb-4 p-3 rounded-4">
        <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
            @csrf

            <div class="d-flex mb-3">
            <img src="{{ Auth::check() ? Auth::user()->getProfileImage() : '/images/default.png' }}" 
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
    @endif

  
    @foreach ($event->comments as $comment)
        <div class="d-flex mb-4" x-data="{ open: false, openEdit:false }">
            <img src="{{ $comment->author->getprofileImage() ?? '/profile/default.png'}}" 
                width="45" height="45"
                class="rounded-circle me-3 border shadow-sm">
            <div class="card border-0 shadow-sm p-3 rounded-4 w-100">
              <strong class="text-dark">{{ $comment->author->username }}</strong>
              <small class="text-muted">{{ \Carbon\Carbon::parse($comment->createdat)->format('d/m/Y H:i') }}</small>
    
              <p class="mb-2">{{ $comment->content }}</p>
      @if(!auth('admin')->check())        
     <div class="d-flex align-items-center mb-2">
       <form method="POST" action="{{ route('comments.vote', $comment) }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary me-2">
            &#x2B06; 
        </button>
    </form>
    <span>{{ $comment->voters()->count() }}</span>
</div>
@endif


              @include('comments.comments_actions', ['comment' => $comment])
              @if(!auth('admin')->check())
                <button class="btn btn-link text-purple p-0"
                        @click="open = !open">
                    Reply
                </button>
                <div x-show="open" x-transition class="mt-3">
                    <form method="POST" action="{{ route('comments.store', ['eventId' => $event->eventid]) }}">
                        @csrf

                        <input type="hidden" name="parentid" value="{{ $comment->commentid }}">

                        <div class="d-flex mb-2">
                            <img src="{{ $comment->author->getprofileImage() ?? '/profile/default.png' }}" 
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
                @endif
                @if ($comment->replies->count())
                    <div class="mt-3 ps-4 border-start">

                        @foreach ($comment->replies as $reply)
                            <div class="d-flex mb-3">

                                <img src="{{ $reply->author->getprofileImage() ?? '/images/default.png' }}" 
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
          
                @if($admin)
                   <span class="badge bg-primary">Admin View</span>
                @endif

       
                 @if($organizer || $admin)
                     <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-purple">
                       Manage Participation
                     </a>
                 @endif
             @if($canSeeExtras)
                 <a href="{{ route('polls.index', $event->eventid) }}" class="btn btn-outline-purple">
                   Polls
                 </a>
             <button id="openPhotos" 
                    class="btn btn-purple" 
                    data-bs-toggle="offcanvas" 
                    data-bs-target="#photosSidebar">
                Photos
            </button>
        @endif   
         @if(!$admin)
            @auth
                @if($isAttendee)
                    <span class="btn btn-success disabled">
                        You are participating
                  </span>
               @elseif($hasPendingRequest)
                    <span class="btn btn-info disabled">
                        Request Pending
                    </span>
                @else
                    <form action="{{ route('events.join', $event->eventid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-purple">
                            Request to Join
                        </button>
                    </form>
                @endif

            @else
                <a href="{{ route('login') }}" class="btn btn-purple">
                    Login to Join
                </a>
            @endauth
          @endif  
                     <a href="{{ $admin ? route('admin.dashboard') : route('dashboard') }}" 
                        class="btn btn-outline-secondary">
                           ← Back to Homepage
                       </a>
                   </div>
                 </div>          
                </div>
            </div>
        </div>
    </div>
</div>



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

<script>
const openPhotosBtn = document.getElementById('openPhotos');
if (openPhotosBtn) {
    openPhotosBtn.addEventListener('click', function () {
        loadPhotos();
    });
}

const viewParticipantsBtn = document.getElementById('viewParticipants');
if (viewParticipantsBtn) {
    viewParticipantsBtn.addEventListener('click', function () {
        loadParticipants();
    });
}

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
                loadPhotos(); // refresh automático
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