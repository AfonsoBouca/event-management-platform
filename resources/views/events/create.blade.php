@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <h2 class="text-center text-purple mb-4">New Event</h2>

                <form method="POST" action="{{ route('events.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="eventname" class="form-label">Event Name</label>
                        <input type="text" name="eventname" id="eventname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startdate" class="form-label">Start Date</label>
                            <input type="datetime-local" name="startdate" id="startdate" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="enddate" class="form-label">End Date</label>
                            <input type="datetime-local" name="enddate" id="enddate" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" name="capacity" id="capacity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (€)</label>
                            <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ispublic" class="form-label">Visibility</label>
                            <select name="ispublic" id="ispublic" class="form-select" required>
                                <option value="1">Public</option>
                                <option value="0">Private</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventstatus" class="form-label">Status</label>
                            <select name="eventstatus" id="eventstatus" class="form-select" required>
                                <option value="Upcoming">Upcoming</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-purple w-100">Create Event</button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('dashboard') }}">← Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
