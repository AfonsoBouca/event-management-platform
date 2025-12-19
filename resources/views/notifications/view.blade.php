@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple mb-0">Notifications</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-purple">
            ← Back to Homepage
        </a>
    </div>

    @if($notifications->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            You have no notifications.
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">

                @foreach($notifications as $n)
                    <div
                        class="list-group-item px-4 py-3 notification-item
                        {{ $n->isread ? '' : 'bg-purple-light border-start border-4 border-purple' }}"
                        data-id="{{ $n->notificationid }}"
                    >

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            {{-- Conteúdo --}}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">
                                    {{ $n->title }}
                                </h6>

                                <p class="mb-2 text-muted">
                                    {{ $n->message }}
                                </p>

                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($n->time)->diffForHumans() }}
                                </small>
                            </div>

                            {{-- Estado + ação --}}
                            <div class="text-end d-flex flex-column align-items-end gap-2 notification-actions">

                                @if($n->isread)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-secondary status-badge">
                                        Unread
                                    </span>

                                    <button
                                        class="btn btn-sm btn-outline-purple mark-read-btn"
                                        data-url="{{ route('notifications.read', $n->notificationid) }}"
                                    >
                                        Mark as read
                                    </button>
                                @endif

                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    @endif
</div>

{{-- SCRIPT AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const url = button.dataset.url;
            const notificationItem = button.closest('.notification-item');
            const actions = button.closest('.notification-actions');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to mark notification as read');
                }

                // Atualização visual
                notificationItem.classList.remove(
                    'bg-purple-light',
                    'border-start',
                    'border-4',
                    'border-purple'
                );

                actions.innerHTML = `
                    <span class="badge bg-success">Read</span>
                `;

            } catch (error) {
                console.error(error);
                alert('Something went wrong. Please try again.');
            }
        });
    });

});
</script>
@endsection
