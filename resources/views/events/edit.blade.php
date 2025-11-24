@extends('layouts.app')

@section('content')

<style>
    .edit-container {
        max-width: 900px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #000;
    }

    .edit-header {
        border-bottom: 2px solid #dda0dd;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .edit-header h1 {
        font-size: 2em;
        color: #6a0dad;
        margin: 0;
    }

    /* Form fields */
    .form-group {
        margin-bottom: 18px;
    }

    label {
        font-weight: bold;
        color: #6a0dad;
        margin-bottom: 5px;
        display: block;
    }

    input, textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #c7b3d9;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 1em;
    }

    textarea {
        min-height: 120px;
    }

    /* Button */
    .btn-save {
        padding: 12px 25px;
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1em;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
</style>

<div class="edit-container">

    <div class="edit-header">
        <h1>Edit Event</h1>
    </div>

    <form action="{{ route('events.updateEvent', $event->eventid) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Event Name</label>
            <input type="text" name="eventname" value="{{ $event->eventname }}" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required>{{ $event->description }}</textarea>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="{{ $event->location }}" required>
        </div>

        <div class="form-group">
            <label>Start Date</label>
            <input type="datetime-local" name="startdate"
                value="{{ \Carbon\Carbon::parse($event->startdate)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group">
            <label>End Date</label>
            <input type="datetime-local" name="enddate"
                value="{{ \Carbon\Carbon::parse($event->enddate)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" value="{{ $event->capacity }}" required>
        </div>

        <div class="form-group">
            <label>Price (€)</label>
            <input type="number" step="0.01" name="price" value="{{ $event->price }}" required>
        </div>

        <button class="btn-save" type="submit">Save Changes</button>
    </form>

</div>

@endsection
