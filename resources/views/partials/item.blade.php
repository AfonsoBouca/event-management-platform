<li class="list-group-item d-flex justify-content-between align-items-center item" data-id="{{$item->id}}">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" @checked($item->done)>
        <span class="{{ $item->done ? 'text-decoration-line-through text-muted' : '' }}">{{ $item->description }}</span>
    </div>
    <a href="#" class="delete text-danger">&times;</a>
</li>