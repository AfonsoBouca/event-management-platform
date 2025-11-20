<form method="'POST" action="'events">
    @csrf
    <input type="text" name="title" placeholder="Event Title" required>
    <textarea name="description" placeholder="Event Description" required></textarea>
    <input type="datetime" name="Start Date" required>
    <input type="datetime" name="End Date" required>
    <input type="number" name="capacity" placeholder="Maximum Capacity" required>
    <input type="number" name="price" placeholder="Price" required>
    <input type="boolean" name="public" placeholder="Is Public?" required>
    <input type="text" name="location" placeholder="Location" required>
    <input type="text" name="status" placeholder="Status" required>
    <button type="submit">Create Event</button>

</form>