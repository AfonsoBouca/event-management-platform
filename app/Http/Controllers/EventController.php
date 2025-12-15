<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Invitation;
use App\Models\EventParticipation;


class EventController extends Controller
{
    // --------------------------------------------------------------------------------------------------------------------------------
    // HOMEPAGE Actions
    // ---------------------------------------------------------------------------------------------------------------------------------
    public function dashboard()
    {
        $upcomingEvents = Event::where('startdate', '<', now())
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

        $query->where('ispublic', true);
    
        if ($request->status) {
            $query->where('eventstatus', ucfirst(strtolower($request->status)));
        }
       
        if ($request->category) {
            $query->whereHas('categories', fn($q) => $q->where('category.categoryname', $request->category));
        }
    
        $events = $query->orderBy('startdate', 'asc')->paginate(3);

        if ($request->ajax()) {
            return view('events.browse', compact('events'))->render();
        }
    
        return view('events.browse', compact('events'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');

        if(!$query || strlen(trim($query)) < 1) {
            return view('search', ['events' => collect(), 'query' => $query]);
        }

        $escaped = str_replace(['%', '|', '!', ':'], ' ', $query);

        $tsquery = implode('&', array_map(function($word) {
            return $word . ':*';
        }, explode(' ', $escaped)));

        $events = Event::whereRaw("event_tsv @@ to_tsquery('english', ?)", [$tsquery])
            ->orderByRaw("ts_rank(event_tsv, to_tsquery('english', ?)) DESC", [$tsquery])
            ->get();

        return view('events.searchresults', compact('events', 'query'));
    }  

    public function ajaxSearch(Request $request)
{
    $query = $request->input('query');

    if (!$query || strlen(trim($query)) < 1) {
        return response()->json([]);
    }

    $escaped = str_replace(['&', '|', '!', ':'], ' ', $query);

    $tsquery = implode(' & ', array_map(function ($w) {
        return $w . ':*';
    }, explode(' ', $escaped)));

    $events = Event::whereRaw("event_tsv @@ to_tsquery('english', ?)", [$tsquery])
        ->orderByRaw("ts_rank(event_tsv, to_tsquery('english', ?)) DESC", [$tsquery])
        ->take(8)
        ->get();

    return response()->json($events);
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

    // --------------------------------------------------------------------------------------------------------------------------------
    // Event Management
    // ---------------------------------------------------------------------------------------------------------------------------------

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
            'enddate'     => 'required|date|after:startdate',
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
            'createdby'   => Auth::user()->userid,
            'eventstatus' => $request->input('eventstatus'),
        ]);

        return redirect()->route('events.show', $event->eventid)
            ->with('success', 'Event created successfully!');
    }

public function editEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $userId = Auth::user()->userid;

        if (!$event->isOrganizer($userId) || $event->createdby !== $userId) {
            return redirect() ->route('user.profile', ['username' => Auth::user()->username])
                ->with('error', 'You do not have permission to delete this event.');
        }

        return view('events.edit', compact('event'));
    }

public function deleteEvent($eventId)
{
    $event = Event::findOrFail($eventId);
    $userId = Auth::user()->userid;

    if (!$event ->isOrganizer($userId) || $event->createdby !== $userId) {
        return redirect() ->route('user.profile', ['username' => Auth::user()->username])
         ->with('error', 'You do not have permission to delete this event.');
    }

    $event->delete();

    return redirect()
        ->route('user.profile', ['username' => Auth::user()->username])
        ->with('success', 'Event deleted successfully.');
}

public function updateEvent(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $userId = Auth::user()->userid;

        // Só o organizador pode atualizar
        if ($event->createdby !== $userId || !$event->isOrganizer($userId)) {
            return redirect()
                ->route('user.profile', ['username' => Auth::user()->username])
                ->with('error', 'You do not have permission to edit this event.');
        }

        // Validação 
        $request->validate([
            'eventname' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Atualiza os campos
        $event->update([
            'eventname' => $request->eventname,
            'description' => $request->description,
            'location' => $request->location,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'capacity' => $request->capacity,
            'price' => $request->price,
        ]);

        return redirect()
            ->route('events.show', $event->eventid)
            ->with('success', 'Event updated successfully.');
    }


    // --------------------------------------------------------------------------------------------------------------------------------
    // User Management
    // ---------------------------------------------------------------------------------------------------------------------------------
    
    
    /**
     * Show event participation management page (organizer only).
     */
    public function manageParticipation($id)
    {
        $event = Event::with(['organizer', 'attendees', 'joinRequests.user'])
            ->findOrFail($id);

        $ispublic = $event->ispublic;
        
        Gate::authorize('manage', $event);
        
        $pendingRequests = $event->joinRequests()
            ->where('j_status', 'Pending')
            ->with('user')
            ->get();
        
        $participants = $event->attendees()->withPivot('role')->get();

        $pendingInvitations = Invitation::where('eventid', $id)
            ->where('confirmation', false)
            ->with('user')
            ->get();

        
        return view('events.manage-participation', compact('event', 'pendingRequests', 'pendingInvitations', 'participants', 'ispublic'));
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

    public function inviteSearch($id)
        {
            $event = Event::findOrFail($id);
            Gate::authorize('manage', $event);
        
            return view('events.invite-users', ['event' => $event, 'users' => [], 'query' => '']);
        }    
    
    public function searchUsers(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        Gate::authorize('manage', $event);
    
        $query = $request->input('q');
        $users = [];
    
        if ($query) {
            $users = User::where('username', 'ILIKE', "%$query%")
                         ->orWhere('email', 'ILIKE', "%$query%")
                         ->take(20)
                         ->get();
        }
        return view('events.invite-users', compact('event', 'users', 'query'));
    }

    public function inviteUser($eventid, $userid)
    {
        $event = Event::findOrFail($eventid);
    
        Gate::authorize('manage', $event);
    
        // Não convidar quem já está no evento ou já foi convidado
        $already = Invitation::where('eventid', $eventid)
                    ->where('userid', $userid)
                    ->first();
    
        if (!$already) {
            Invitation::create([
                'eventid' => $eventid,
                'userid' => $userid,
                'confirmation' => false
            ]);
        }
    
        return back()->with('status', 'Invitation sent!');
    }
    
    public function acceptInvitation($invitationid)
    {
        $inv = Invitation::findOrFail($invitationid);
    
        if ($inv->userid !== Auth::user()->userid) {
            abort(403, "Not authorized");
        }
    
        $inv->confirmation = true;
        $inv->save();
    
        EventParticipation::firstOrCreate([
            'userid' => $inv->userid,
            'eventid' => $inv->eventid
        ]);
    
        return back()->with('success', 'Invitation accepted!');
    }
    
    public function rejectInvitation($invitationid)
    {
        $inv = Invitation::findOrFail($invitationid);
    
        if ($inv->userid !== Auth::user()->userid) {
            abort(403, "Not authorized");
        }
    
        $inv->delete();
    
        return back()->with('success', 'Invitation rejected');
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
            ->with('success', 'Participant removed successfully.');
    }

    /**
     * Allow an attendee to leave an event they are participating in.
     */
    public function leaveEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $userId = Auth::id();  
        
        if ($event->createdby == $userId) {
            return redirect()
                ->route('events.show', $eventId)
                ->with('error', 'The organizer cannot leave their own event.');
        }
            
        $participation = DB::table('eventparticipation')
            ->where('eventid', $eventId)
            ->where('userid', $userId)
            ->first();
        
        if (!$participation) {
            return redirect()
                ->route('events.show', $eventId)
                ->with('error', 'You are not participating in this event.');
        }
        
        DB::table('eventparticipation')
            ->where('eventid', $eventId)
            ->where('userid', $userId)
            ->delete();
        
        return redirect()
            ->route('events.show', $eventId)
            ->with('success', 'You have left the event successfully.');
    }


    public function loadPhotos($eventId)
    {
        $photos = \DB::table('eventphoto')
            ->where('eventid', $eventId)
            ->orderBy('createdat', 'desc')
            ->get();

        // futuramente podes buscar fotos da DB
        return view('events.partials.photos', [
            'eventId' => $eventId,
            'photos' => $photos
        ]);
    }


public function viewParticipation($id)
{
    $event = Event::with(['organizer', 'attendees'])->findOrFail($id);

    $user = Auth::user();


    if (!$user || (!$event->isOrganizer($user->userid) && !$event->isAttendee($user->userid))) {
        abort(403);
    }

    $participants = $event->attendees()->withPivot('role')->get();

    return view('events.partials.participants', compact('event', 'participants'));
}
   
    


}    
      