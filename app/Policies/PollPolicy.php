<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;
use App\Models\Event;

class PollPolicy
{
    /**
     * Determine if the user can view polls for an event.
     * Only organizers and attendees can view polls.
     */
    public function viewAny(User $user, Event $event): bool
    {
        return $event->isOrganizer($user->userid) || $event->isAttendee($user->userid);
    }

    /**
     * Determine if the user can view a specific poll.
     */
    public function view(User $user, Poll $poll): bool
    {
        $event = $poll->event;
        return $event->isOrganizer($user->userid) || $event->isAttendee($user->userid);
    }

    /**
     * Determine if the user can create polls.
     * Only organizers can create polls.
     */
    public function create(User $user, Event $event): bool
    {
        return $event->isOrganizer($user->userid);
    }

    /**
     * Determine if the user can vote in a poll.
     * Only attendees can vote (not organizers).
     */
    public function vote(User $user, Poll $poll): bool
    {
        $event = $poll->event;
        
        // Must be an attendee
        if (!$event->isAttendee($user->userid)) {
            return false;
        }
        
        // Cannot vote if already voted
        if ($poll->hasUserVoted($user->userid)) {
            return false;
        }
        
        return true;
    }

    /**
     * Determine if the user can delete a poll.
     * Only organizers can delete polls.
     */
    public function delete(User $user, Poll $poll): bool
    {
        $event = $poll->event;
        return $event->isOrganizer($user->userid);
    }
}
