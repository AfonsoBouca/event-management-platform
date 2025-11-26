@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-purple text-white">
                    <h4 class="mb-0">Edit Event</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.updateEvent', $event->eventid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="eventname" class="form-label">Event Name</label>
                            <input type="text" name="eventname" id="eventname" class="form-control" value="{{ $event->eventname }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required>{{ $event->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ $event->location }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label">Capacity</label>
                                <input type="number" name="capacity" id="capacity" class="form-control" value="{{ $event->capacity }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="startdate" class="form-label">Start Date</label>
                                <input type="datetime-local" name="startdate" id="startdate" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($event->startdate)->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="enddate" class="form-label">End Date</label>
                                <input type="datetime-local" name="enddate" id="enddate" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($event->enddate)->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price (€)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $event->price }}" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-purple" type="submit">Save Changes</button>
                            <a href="{{ route('events.show', $event->eventid) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
