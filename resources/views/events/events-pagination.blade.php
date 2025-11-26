
@if ($events->hasPages())
    <div class="pagination-container">
        @if (!$events->onFirstPage())
            <a href="{{ $events->previousPageUrl() }}" class="page-link" data-page="{{ $events->currentPage() - 1 }}">&laquo; Prev</a>
        @endif

        @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $events->currentPage() == $page ? 'active' : '' }}" data-page="{{ $page }}">{{ $page }}</a>
        @endforeach

        @if ($events->hasMorePages())
            <a href="{{ $events->nextPageUrl() }}" class="page-link" data-page="{{ $events->currentPage() + 1 }}">Next &raquo;</a>
        @endif
    </div>
@endif
