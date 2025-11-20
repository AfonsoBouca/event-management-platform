<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
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
}
