<div class="col-md-4">
    <div class="card shadow-sm h-100" data-id="{{ $card->id }}">
        <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <a href="{{ route('cards.show', $card) }}" class="text-white text-decoration-none">
                    {{ $card->name }}
                </a>
            </h5>
            <a href="#" class="delete text-white-50">&times;</a>
        </div>
        <ul class="list-group list-group-flush">
            {{-- Render each item belonging to this card --}}
            @each('partials.item', $card->items, 'item')
        </ul>
        <div class="card-footer bg-transparent">
            <form class="new_item">
                <input type="text" name="description" class="form-control form-control-sm" placeholder="Add new item...">
            </form>
        </div>
    </div>
</div>