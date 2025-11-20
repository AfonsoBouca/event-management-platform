<form method="POST" action="/events">
    @csrf

    <label for="title">Event Title</label>
    <input type="text" name="title" placeholder="Event Title" required>
    
    <label for="description">Event Description</label>
    <textarea name="description" placeholder="Event Description" required></textarea>
    
    <label for="startDate">Start Date</label>
    <input type="datetime-local" name="startDate" id="startDate" required>
    
    <label for="endDate">End Date</label>
    <input type="datetime-local" name="endDate" id="endDate" required>
    
    <label for="capacity">Maximum Capacity</label>
    <input type="number" name="capacity" id="capacity" min="1" placeholder="Maximum Capacity" required>
    
    <label for="price">Price</label>
    <input type="number" name="price" id="price" min="0" placeholder="Price in $" required>
    <small>Use 0 if the event is free</small>
    
    <label for="isPublic">Visibility</label>
    <select name="isPublic" id="isPublic" required>
        <option value="public">Public</option>
        <option value="private">Private</option>
    </select>
    
    <input type="text" name="location" placeholder="Location" required>
    
    <label for="status">Status</label>
    <select name="status" id="status" required>
        <option value="upcoming">Upcoming</option>
        <option value="ongoing">Ongoing</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
    
    <button type="submit">Create Event</button>
</form>
