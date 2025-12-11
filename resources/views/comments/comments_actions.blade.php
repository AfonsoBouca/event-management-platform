@can('update', $comment)
<div x-data="{ openEdit: false }">

    <div class="mb-2">
        <button class="btn btn-sm btn-link text-purple p-0 me-2"
                @click="openEdit = !openEdit">
            Edit
        </button>

        @can('delete', $comment)
        <form method="POST" action="{{ route('comments.delete', $comment) }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Delete</button>
        </form>
        @endcan
    </div>

    <form x-show="openEdit" method="POST" 
          action="{{ route('comments.edit', $comment) }}" 
          class="mb-2">
        @csrf
        @method('PUT')
        <textarea name="content" class="form-control mb-1" rows="2">{{ $comment->content }}</textarea>

        <button type="submit" class="btn btn-sm btn-warning">Save</button>
        <button type="button" class="btn btn-sm btn-secondary"
                @click="openEdit = false">Cancel</button>
    </form>

</div>
@endcan
