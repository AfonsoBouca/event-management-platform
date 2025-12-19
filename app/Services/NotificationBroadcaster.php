<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationBroadcaster
{
    public static function broadcastLatestForUser(int $userId): void
    {
        $notification = Notification::where('userid', $userId)
            ->orderBy('notificationid', 'desc')
            ->first();

        if ($notification) {
            event(new NotificationCreated($notification->toArray()));
        }
    }
}
