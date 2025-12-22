<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Models\Comment;


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

    public function comment(User $user, Event $event){
        return $event->isAttendee($user->userid);
    }

    public function viewComments(User $user, Event $event){
        return $this->comment($user,$event);
    }

     public function viewMessages(User $user, Event $event)
    {
        return $event->isOrganizer($user->userid)
            || $event->isAttendee($user->userid);
    }

    public function postMessage(User $user, Event $event)
    {
        return $event->isOrganizer($user->userid);
    }

}