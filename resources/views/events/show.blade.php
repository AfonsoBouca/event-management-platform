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

            <div class="card-footer bg-white">
                <div class="d-flex flex-wrap gap-2">
                    @auth
                        @if(Auth::check() && $event->createdby === Auth::user()->userid)
                            <a href="{{ route('events.manage', $event->eventid) }}" class="btn btn-purple">Manage Participation</a>
                        @endif

                        @if($isAttendee)
                            <span class="btn btn-success disabled">You are participating</span>
                            <button id="openPhotos" class="btn btn-purple" data-bs-toggle="offcanvas" data-bs-target="#photosSidebar"> Photos </button>
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

<!-- <div id="photosSidebar" 
     style="position: fixed; top:0; right:-400px; width:400px; height:100%;
            background:white; box-shadow:-2px 0 10px rgba(0,0,0,0.2);
            transition: right 0.3s ease; padding:20px; z-index:9999;">
    
    <button id="closeSidebar" class="btn btn-outline-secondary mb-3">Close</button>

    <div id="photosContent">
        
    </div>
</div> -->

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

<script>
document.getElementById('openPhotos')
    .addEventListener('click', function () {
        loadPhotos();
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
                loadPhotos(); // refresh automático
            }
        });
    });
}
</script>




@endsection