@extends('layouts.app')

@section('content')
<style>
    .container {
        max-width: 800px;
        margin: 40px auto;
        background-color:rgb(255, 255, 255); 
        padding: 25px;
        border-radius: 8px;
        color: #000;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #dda0dd; 
    }

    form label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #000;
    }

    form input,
    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #444;
        border-radius: 5px;
        background-color:rgb(255, 251, 251);
        color: #000;
        box-sizing: border-box;
    }

    form textarea {
        resize: vertical;
    }

    button[type="submit"] {
        margin-top: 20px;
        padding: 10px 20px;
        background: linear-gradient(45deg, #6a0dad, #7d1dcf);
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    button[type="submit"]:hover {
        background: linear-gradient(45deg, #7d1dcf, #9a33e0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }

    .back-home {
        display: inline-block;
        margin-top: 15px;
        color: #6a0dad;
        text-decoration: none;
    }

    .back-home:hover {
        text-decoration: underline;
        color: #7d1dcf;
    }
</style>

<div class="container">
    <h1>New Event</h1>

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
            <option value="1">Público</option>
            <option value="0">Privado</option>
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

    <a class="back-home" href="{{ route('dashboard') }}">← Back to Homepage</a>
</div>
@endsection
