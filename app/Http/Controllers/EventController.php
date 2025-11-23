<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function dashboard()
    {
        $upcomingEvents = Event::where('startdate', '>', now())
            ->where('ispublic', true)
            ->orderBy('startdate', 'desc')
            ->take(5)
            ->get();

        $recentEvents = Event::orderBy('eventid', 'desc')
            ->where('ispublic', true)
            ->take(5)
            ->get();

        return view('events.dashboard', compact('upcomingEvents', 'recentEvents'));
    }

    public function browse(Request $request)
    {
        $query = Event::query();
    
        if ($request->has('status') && $request->status != '') {
            $status = ucfirst(strtolower($request->status));
            $query->where('eventstatus', $status);
        }
       
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
    
        $events = $query->orderBy('startdate', 'asc')->paginate(10);
    
        return view('events.browse', compact('events'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');

        $events = Event::where('ispublic', true)
            ->where(function ($q) use ($query) {
                $q->where('eventname', 'ILIKE', '%' . $query . '%')
                  ->orWhere('description', 'ILIKE', '%' . $query . '%');
            })
            ->get();

        return view('events.searchresults', compact('events', 'query'));
    }

    public function show($id)
    {
        $event = Event::with(['organizer', 'attendees'])->findOrFail($id);
        
        $user = Auth::user();
        
        if (!$event->ispublic) {
            if (!$user) {
                abort(403, 'This event is private.');
            }
            
            $isOrganizer = $event->createdby === $user->userid;
            $isAttendee = $event->isAttendee($user->userid);
            
            if (!$isOrganizer && !$isAttendee) {
                abort(403, 'You do not have permission to view this private event.');
            }
        }
        
        $hasPendingRequest = false;
        $isAttendee = false;
        
        if ($user) {
            $isAttendee = $event->isAttendee($user->userid);
            
            $hasPendingRequest = JoinRequest::where('eventid', $id)
                ->where('userid', $user->userid)
                ->where('j_status', 'Pending')
                ->exists();
        }
        
        return view('events.show', compact('event', 'isAttendee', 'hasPendingRequest'));
    }

    public function requestToJoin($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to join events.');
        }
        
        $event = Event::findOrFail($id);
        
        if ($event->isAttendee($user->userid)) {
            return redirect()->route('events.show', $id)
                ->with('info', 'You are already a participant of this event.');
        }
        
        $existingRequest = JoinRequest::where('eventid', $id)
            ->where('userid', $user->userid)
            ->where('j_status', 'Pending')
            ->first();
            
        if ($existingRequest) {
            return redirect()->route('events.show', $id)
                ->with('info', 'You already have a pending request for this event.');
        }
        
        JoinRequest::create([
            'eventid' => $id,
            'userid' => $user->userid,
            'j_status' => 'Pending',
        ]);
        
        return redirect()->route('events.show', $id)
            ->with('success', 'Your request to join has been sent!');
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $request->validate([
            'eventname'   => 'required|string|max:255',
            'description' => 'required|string',
            'startdate'   => 'required|date',
            'enddate'     => 'required|date|after_or_equal:startdate',
            'capacity'    => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'ispublic'    => 'required|boolean',
            'location'    => 'required|string|max:255',
            'eventstatus' => 'required|in:Upcoming,Ongoing,Completed,Cancelled',
        ]);

        $event = Event::create([
            'eventname'   => $request->input('eventname'),
            'description' => $request->input('description'),
            'startdate'   => $request->input('startdate'),
            'enddate'     => $request->input('enddate'),
            'capacity'    => $request->input('capacity'),
            'price'       => $request->input('price'),
            'ispublic'    => $request->input('ispublic'),
            'location'    => $request->input('location'),
            'createdby'   => Auth::id(),
            'eventstatus' => $request->input('eventstatus'),
        ]);

        return redirect()->route('events.show', $event->eventid)
            ->with('success', 'Event created successfully!');
    }

    /**
     * Show event participation management page (organizer only).
     */
    public function manageParticipation($id)
    {
        $event = Event::with(['organizer', 'attendees', 'joinRequests.user'])
            ->findOrFail($id);
        
        Gate::authorize('manage', $event);
        
        $pendingRequests = $event->joinRequests()
            ->where('j_status', 'Pending')
            ->with('user')
            ->get();
        
        $participants = $event->attendees()->withPivot('role')->get();
        
        return view('events.manage-participation', compact('event', 'pendingRequests', 'participants'));
    }

    /**
     * Approve a join request.
     */
    public function approveJoinRequest($eventId, $joinId)
    {
        $event = Event::findOrFail($eventId);
        Gate::authorize('manage', $event);
        
        $joinRequest = JoinRequest::where('joinid', $joinId)
            ->where('eventid', $eventId)
            ->firstOrFail();
        
        $joinRequest->j_status = 'Accepted';
        $joinRequest->save();
        
        return redirect()
            ->route('events.manage', $eventId)
            ->with('success', 'Pedido aprovado com sucesso!');
    }

    /**
     * Reject a join request.
     */
    public function rejectJoinRequest($eventId, $joinId)
    {
        $event = Event::findOrFail($eventId);
        Gate::authorize('manage', $event);
        
        $joinRequest = JoinRequest::where('joinid', $joinId)
            ->where('eventid', $eventId)
            ->firstOrFail();
        
        $joinRequest->j_status = 'Rejected';
        $joinRequest->save();
        
        return redirect()
            ->route('events.manage', $eventId)
            ->with('success', 'Pedido rejeitado.');
    }

    /**
     * Remove a participant from the event.
     */
    public function removeParticipant($eventId, $userId)
    {
        $event = Event::findOrFail($eventId);
        Gate::authorize('manage', $event);
        
        if ($event->createdby == $userId) {
            return redirect()
                ->route('events.manage', $eventId)
                ->with('error', 'Não podes remover o organizador do evento.');
        }
        
        DB::table('eventparticipation')
            ->where('eventid', $eventId)
            ->where('userid', $userId)
            ->delete();
        
        return redirect()
            ->route('events.manage', $eventId)
            ->with('success', 'Participante removido com sucesso.');
    }
}