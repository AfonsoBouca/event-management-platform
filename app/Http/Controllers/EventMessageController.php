<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\EventMessage;
use App\Services\NotificationBroadcaster;

class EventMessageController extends Controller
{
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $this->authorize('viewMessages', $event);
        $admin = auth('admin')->check();
        $user = auth()->user();

        // organizer OU attendee
        if (!$admin && (!$user || (!$event->isOrganizer($user->userid) && !$event->isAttendee($user->userid)))) {
            abort(403);
        }

        $messages = EventMessage::with('author')
            ->where('eventid', $eventId)
            ->orderBy('createdat', 'desc')
            ->get();

        return view('events.partials.messages', compact('messages', 'event'));
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $this->authorize('postMessage', $event);

        // apenas organizer
        if (!$event->isOrganizer(auth()->id())) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        EventMessage::create([
            'eventid' => $eventId,
            'authorid' => auth()->id(),
            'content' => $request->content
        ]);

        $attendees = $event->attendees()->pluck('user.userid');
            
        foreach ($attendees as $userId) {
            NotificationBroadcaster::broadcastLatestForUser($userId);
        }

        return response()->json(['success' => true]);
    }
}
