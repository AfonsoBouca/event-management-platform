<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public array $notification;

    public function __construct(array $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn(): array
    {
        Log::info('Broadcasting notification', $this->notification);

        $uid = $this->notification['userId']
            ?? $this->notification['userid']
            ?? null;

        Log::info('Broadcast channel', ['channel' => 'user.' . $uid]);

        return [
            new Channel('user.' . $uid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }
}
