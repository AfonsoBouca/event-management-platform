<h4 class="text-purple mb-3">Photos of the Event</h4>

<form id="photoUploadForm" enctype="multipart/form-data" class="mb-3">
    @csrf
    <input type="file" name="photo" class="form-control mb-2" required>
    <button class="btn btn-purple w-100">Upload</button>
</form>

@if($photos->isEmpty())
    <p class="text-center text-muted">Be the first to upload </p>
@else
    <div class="row g-2">
        @foreach ($photos as $p)
            <div>
                
                <img src="{{ asset('event/' . $p->filepath) }}" width="200">
            </div>
        @endforeach
    </div>
@endif
