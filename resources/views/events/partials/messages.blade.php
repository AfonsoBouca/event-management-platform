{{-- Only organizer sees the form --}}
@can('postMessage', $event)
<form id="messageForm" class="mb-4">
    @csrf
    <textarea class="form-control mb-2"
              name="content"
              rows="3"
              placeholder="Post an important update..."></textarea>
    <button class="btn btn-purple btn-sm">Post Message</button>
</form>
@endcan

{{-- Messages List --}}

@forelse($messages as $msg)
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <strong>{{ $msg->author->username }}</strong>
        <small class="text-muted ms-2">
            {{ \Carbon\Carbon::parse($msg->createdat)->format('d/m/Y H:i') }}
        </small>
        <p class="mt-2 mb-0">{{ $msg->content }}</p>
    </div>
</div>
@empty
<p class="text-muted text-center">No messages yet.</p>
@endforelse
