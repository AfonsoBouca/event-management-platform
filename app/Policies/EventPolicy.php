<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine if the user can manage the event (organizer only).
     */
    public function manage(User $user, Event $event): bool
    {
        return $user->userid === $event->createdby;
    }

    /**
     * Determine if the user can view the event.
     * Public events can be viewed by anyone.
     * Private events only by organizer or participants.
     */
    public function view(?User $user, Event $event): bool
    {
        if ($event->ispublic) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($event->createdby === $user->userid) {
            return true;
        }

        return $event->isAttendee($user->userid);
    }
}