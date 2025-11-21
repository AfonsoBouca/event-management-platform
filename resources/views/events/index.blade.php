<form method="POST" action="/events">
    @csrf
    <title>Create New Event</title>
    
    <br><label for="title">Event Title</label>
    <br><input type="text" name="title" placeholder="Event Title" required>

    <br><label for="description">Event Description</label>
    <br><textarea name="description" placeholder="Event Description" required></textarea>
    
    <br><label for="startDate">Start Date</label>
    <br><input type="datetime-local" name="startDate" id="startDate" required>
    
    <br><label for="endDate">End Date</label>
    <br><input type="datetime-local" name="endDate" id="endDate" required>
    
    <br><label for="capacity">Maximum Capacity</label>
    <br><input type="number" name="capacity" id="capacity" min="1" placeholder="Maximum Capacity" required>
    
    <br><label for="price">Price</label>
    <br><input type="number" name="price" id="price" min="0" placeholder="Price in $" required>
    <small>Use 0 if the event is free</small>
    
    <br><label for="isPublic">Visibility</label>
    <br><select name="isPublic" id="isPublic" required>
        <option value="public">Public</option>
        <option value="private">Private</option>
    </select>
    
    <br><input type="text" name="location" placeholder="Location" required>
    
    <br><label for="status">Status</label>
    <br><select name="status" id="status" required>
        <option value="upcoming">Upcoming</option>
        <option value="ongoing">Ongoing</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
    
    <br><button type="submit">Create Event</button>
</form>
