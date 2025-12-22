@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2>All Reports</h2>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reporter</th>
                <th>Type</th>
                <th>Status</th>
                <th>Related To</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($reports as $r)
            <tr>
                <td>{{ $r->reportid }}</td>
                <td>{{ $r->reporter->username ?? 'Unknown' }}</td>
                <td>{{ $r->reporttype }}</td>
                <td>
                    <span class="badge 
                        @if($r->status === 'Pending') bg-warning
                        @elseif($r->status === 'Accepted') bg-success
                        @elseif($r->status === 'Rejected') bg-danger
                        @endif">
                        {{ $r->status }}
                    </span>
                </td>
                <td>
                    @if($r->event->count()) Event #{{ $r->event->first()->eventid }} <br> @endif
                    @if($r->comment->count()) Comment #{{ $r->comment->first()->commentid }} <br> @endif
                    @if($r->reportedUser->count()) User #{{ $r->reportedUser->first()->userid }} @endif
                </td>
                <td>{{ $r->reporttime }}</td>
                <td>
                    <!-- Botão para abrir modal de detalhes -->
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal{{ $r->reportid }}">
                        View
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reports->links() }}

</div>

<!-- Modals para cada report -->
@foreach ($reports as $r)
<div class="modal fade" id="reportModal{{ $r->reportid }}" tabindex="-1" aria-labelledby="reportModalLabel{{ $r->reportid }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel{{ $r->reportid }}">Report Details #{{ $r->reportid }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Type:</strong> {{ $r->reporttype }}</p>
                <p><strong>Description:</strong> {{ $r->description ?? 'No description' }}</p>
                <p><strong>Related To:</strong>
                    @if($r->event->count()) Event #{{ $r->event->first()->eventid }} <br> @endif
                    @if($r->comment->count()) Comment #{{ $r->comment->first()->commentid }} <br> @endif
                    @if($r->reportedUser->count()) User #{{ $r->reportedUser->first()->userid }} @endif
                    @if(!$r->event->count() && !$r->comment->count() && !$r->reportedUser->count()) N/A @endif
                </p>
                <p><strong>Status:</strong> {{ $r->status }}</p>
            </div>
            <div class="modal-footer">
                @if($r->status === 'Pending')
                    <form action="{{ route('admin.reports.updateStatus', $r->reportid) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Accepted">
                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                    </form>
                    <form action="{{ route('admin.reports.updateStatus', $r->reportid) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                @else
                    {{ $r->status }}
                @endif
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
