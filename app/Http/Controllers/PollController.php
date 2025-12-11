<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    /**
     * Display all polls for an event.
     * GET /events/{id}/polls
     */
    public function index(int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        // Check if user can view polls (must be attendee or organizer)
        if (!$event->isOrganizer($user->userid) && !$event->isAttendee($user->userid)) {
            return redirect()->route('events.show', $eventId)
                ->with('error', 'You must be a participant to view polls.');
        }

        $polls = Poll::where('eventid', $eventId)
            ->with(['options.votes'])
            ->orderBy('createdat', 'desc')
            ->get();

        return view('polls.index', compact('event', 'polls'));
    }

    /**
     * Show form to create a new poll.
     * GET /events/{id}/polls/create
     */
    public function create(int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        // Only organizers can create polls
        if (!$event->isOrganizer($user->userid)) {
            return redirect()->route('events.show', $eventId)
                ->with('error', 'Only the organizer can create polls.');
        }

        return view('polls.create', compact('event'));
    }

    /**
     * Store a new poll.
     * POST /events/{id}/polls
     */
    public function store(Request $request, int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        // Only organizers can create polls
        if (!$event->isOrganizer($user->userid)) {
            return redirect()->route('events.show', $eventId)
                ->with('error', 'Only the organizer can create polls.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        // Create the poll
        $poll = Poll::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'eventid' => $eventId,
            'createdat' => now(),
        ]);

        // Create the options
        foreach ($validated['options'] as $optionText) {
            PollOption::create([
                'pollid' => $poll->pollid,
                'optiontext' => $optionText,
            ]);
        }

        return redirect()->route('polls.index', $eventId)
            ->with('success', 'Poll created successfully.');
    }

    /**
     * Vote in a poll.
     * POST /polls/{id}/vote
     */
    public function vote(Request $request, int $pollId)
    {
        $poll = Poll::with('options')->findOrFail($pollId);
        $event = $poll->event;
        $user = Auth::user();

        // Check if user is an attendee (only attendees can vote)
        if (!$event->isAttendee($user->userid)) {
            return redirect()->route('events.show', $event->eventid)
                ->with('error', 'Only event attendees can vote in polls.');
        }

        // Check if user already voted
        if ($poll->hasUserVoted($user->userid)) {
            return redirect()->route('polls.index', $event->eventid)
                ->with('error', 'You have already voted in this poll.');
        }

        $validated = $request->validate([
            'option_id' => 'required|integer|exists:polloption,polloptionid',
        ]);

        // Verify the option belongs to this poll
        $option = PollOption::where('polloptionid', $validated['option_id'])
            ->where('pollid', $pollId)
            ->firstOrFail();

        // Create the vote
        PollVote::create([
            'polloptionid' => $option->polloptionid,
            'userid' => $user->userid,
            'votedat' => now(),
        ]);

        return redirect()->route('polls.index', $event->eventid)
            ->with('success', 'Your vote has been recorded.');
    }

    /**
     * Get poll results.
     * GET /polls/{id}/results
     */
    public function results(int $pollId)
    {
        $poll = Poll::with(['options.votes', 'event'])->findOrFail($pollId);
        $event = $poll->event;
        $user = Auth::user();

        // Check if user can view results (must be attendee or organizer)
        if (!$event->isOrganizer($user->userid) && !$event->isAttendee($user->userid)) {
            return redirect()->route('events.show', $event->eventid)
                ->with('error', 'You must be a participant to view poll results.');
        }

        $results = [
            'poll' => $poll,
            'totalVotes' => $poll->total_votes,
            'options' => $poll->options->map(function ($option) {
                return [
                    'id' => $option->polloptionid,
                    'text' => $option->optiontext,
                    'voteCount' => $option->vote_count,
                    'percentage' => $option->percentage,
                ];
            }),
        ];

        // For AJAX requests, return JSON
        if (request()->expectsJson()) {
            return response()->json($results);
        }

        return view('polls.results', compact('poll', 'event', 'results'));
    }

    /**
     * Delete a poll.
     * DELETE /polls/{id}
     */
    public function destroy(int $pollId)
    {
        $poll = Poll::findOrFail($pollId);
        $event = $poll->event;
        $user = Auth::user();

        // Only organizers can delete polls
        if (!$event->isOrganizer($user->userid)) {
            return redirect()->route('events.show', $event->eventid)
                ->with('error', 'Only the organizer can delete polls.');
        }

        $poll->delete();

        return redirect()->route('polls.index', $event->eventid)
            ->with('success', 'Poll deleted successfully.');
    }
}
