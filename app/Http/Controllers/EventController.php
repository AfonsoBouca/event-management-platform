<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // Dashboard de eventos
    public function dashboard()
    {
        $upcomingEvents = Event::where('startdate', '>', now())
            ->orderBy('startdate', 'desc')
            ->get();

        $recentEvents = Event::orderBy('eventid', 'desc')
            ->take(5)
            ->get();

        return view('events.dashboard', compact('upcomingEvents', 'recentEvents'));
    }

    // Barra de pesquisa
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

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        // Confirma que o usuário está logado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        // Validação dos dados
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

        // Cria o evento
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

        if ($event) {
            return redirect()->route('user.profile', auth()->user()->userid)
                             ->with('success', 'Event created successfully!');
        }
        return back()->with('error', 'Failed to create event.');        

    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events.show', compact('event'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.create');
    }

    public function favorites($id)
    {
        $events = Event::whereHas('favorites', fn($q) => $q->where('userid', $id))->get();
        return view('events.favorites', compact('events'));
    }

    public function attending($id)
    {
        $events = Event::whereHas('attendees', fn($q) => $q->where('userid', $id))->get();
        return view('events.attending', compact('events'));
    }

    public function organizing($id)
    {
        $events = Event::where('createdby', $id)->get();
        return view('events.organizing', compact('events'));
    }
}
