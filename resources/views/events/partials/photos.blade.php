<h4 class="text-purple mb-3">Photos of the Event</h4>

<form id="photoUploadForm" enctype="multipart/form-data" class="mb-3">
    @csrf
    <input type="file" name="photo" class="form-control mb-2" required>
    <button class="btn btn-purple w-100">Upload</button>
</form>

@if($photos->isEmpty())
    <p class="text-center text-muted">Be the first to upload </p>
@else
    <div class="d-flex flex-column gap-3">
    @foreach ($photos as $p)
        <img 
            src="{{ asset('event/' . $p->filepath) }}" 
            class="img-fluid rounded border"
            style="max-width: 100%; height: auto;"
        >
        <small class="text-muted">
            Uploaded by <strong>{{ $p->username }}</strong>
        </small>

        
    @endforeach
</div>
@endif



