@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h1 class="mb-4">{{ auth()->user()->username }}</h1>

  
    <div class="mb-4">
        <a href="{{ route('admin.browse') }}" class="btn btn-primary">Browse All Events</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-warning">View All Reports</a>
        <a href="{{ route('admin.banned.users') }}" class="btn btn-danger">Banned Users</a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                    <h6 class="card-title mt-2">Total Users</h6>
                    <h3>{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-calendar-event fs-3 text-success"></i>
                    <h6 class="card-title mt-2">Total Events</h6>
                    <h3>{{ $totalEvents }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle fs-3 text-warning"></i>
                    <h6 class="card-title mt-2">Reports Pending</h6>
                    <h3>{{ $pendingReports }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-person-plus fs-3 text-info"></i>
                    <h6 class="card-title mt-2">Join Requests Pending</h6>
                    <h3>{{ $pendingJoinRequests }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Events by Status</h6>
                    @foreach($eventsByStatus as $status)
                        <p class="mb-1">
                            <span class="badge 
                                @if($status->eventstatus === 'Upcoming') bg-info
                                @elseif($status->eventstatus === 'Ongoing') bg-success
                                @elseif($status->eventstatus === 'Completed') bg-secondary
                                @elseif($status->eventstatus === 'Cancelled') bg-danger
                                @endif">
                                {{ $status->eventstatus }}
                            </span>: {{ $status->total }}
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    

</div>
<!-- Last Reports -->
<div class="card mb-4">
    <div class="card-header">Last Reports</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Reporter</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestReports as $report)
                    <tr>
                        <td>{{ $report->reportid }}</td>
                        <td>{{ $report->reporter->username ?? 'Unknown' }}</td>
                        <td>{{ $report->reporttype }}</td>
                        <td>
                            <span class="badge 
                                @if($report->status === 'Pending') bg-warning
                                @elseif($report->status === 'Accepted') bg-success
                                @elseif($report->status === 'Rejected') bg-danger
                                @endif">
                                {{ $report->status }}
                            </span>
                        </td>
                        <td>
                            @if($report->status === 'Pending')
                                <form action="{{ route('admin.reports.updateStatus', $report->reportid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Accepted">
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form action="{{ route('admin.reports.updateStatus', $report->reportid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @else
                                {{ $report->status }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection
