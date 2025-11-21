<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    //dashboard de eventos
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

    //barra de pesquisa
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        // Corrige nomes das colunas para minúsculas
        $events = Event::where('ispublic', true)
            ->where(function ($q) use ($query) {
                $q->where('eventname', 'ILIKE', '%' . $query . '%')  // ILIKE = case-insensitive
                  ->orWhere('description', 'ILIKE', '%' . $query . '%');
            })
            ->get();
    
        // Passa o query também para a view
        return view('events.searchresults', compact('events', 'query'));

    }
    
    
    public function index()
    {
        $events = Event::all();
        return view('events.index', ['events' => $events]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'eventName' => 'required|string|max:255',
            'description' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'capicity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'isPublic' => 'required|boolean',
            'location' => 'required|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        Event::create([
            'eventName' => $request->input('eventName'),
            'description' => $request->input('description'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'capicity' => $request->input('capicity'),
            'price' => $request->input('price'),
            'isPublic' => $request->input('isPublic'),
            'location' => $request->input('location'),
            'createdBy' => auth()->id(),
            'status' => $request->input('status'),
        ]);

        return redirect('/');
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

        return redirect('events.index');
    }

    public function favorites($id) {
        $events = Event::whereHas('favorites', fn($q) => $q->where('userd', $id))->get();
        return view('events.favorites', compact('events'));
    }
    
    public function attending($id) {
        $events = Event::whereHas('attendees', fn($q) => $q->where('userid', $id))->get();
        return view('events.attending', compact('events'));
    }
    
    public function organizing($id) {
        $events = Event::where('createdBy', $id)->get();
        return view('events.organizing', compact('events'));
    }
    

}
