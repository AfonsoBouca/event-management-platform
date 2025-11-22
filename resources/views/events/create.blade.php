@extends('layouts.app')

@section('content')
<h1>Create New Event</h1>

<form method="POST" action="{{ route('events.store') }}">
    @csrf

    <label for="eventname">Event Name</label>
    <input type="text" name="eventname" id="eventname" required>

    <label for="description">Description</label>
    <textarea name="description" id="description" required></textarea>

    <label for="startdate">Start Date</label>
    <input type="datetime-local" name="startdate" id="startdate" required>

    <label for="enddate">End Date</label>
    <input type="datetime-local" name="enddate" id="enddate" required>

    <label for="capacity">Capacity</label>
    <input type="number" name="capacity" id="capacity" min="1" required>

    <label for="price">Price</label>
    <input type="number" name="price" id="price" min="0" required>

    <label for="ispublic">Visibility</label>
    <select name="ispublic" id="ispublic" required>
        <option value="1">Public</option>
        <option value="0">Private</option>
    </select>

    <label for="location">Location</label>
    <input type="text" name="location" id="location" required>

    <label for="eventstatus">Status</label>
    <select name="eventstatus" id="eventstatus" required>
        <option value="Upcoming">Upcoming</option>
        <option value="Ongoing">Ongoing</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
    </select>

    <button type="submit">Create Event</button>
</form>
@endsection
