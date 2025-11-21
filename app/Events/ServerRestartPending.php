<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServerRestartPending implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $minutesRemaining;

    public ?string $eventName;

    public ?int $eventId;

    public string $scheduledRestartTime;

    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $minutesRemaining,
        ?string $eventName,
        ?int $eventId,
        string $scheduledRestartTime,
        string $timestamp
    ) {
        $this->minutesRemaining = $minutesRemaining;
        $this->eventName = $eventName;
        $this->eventId = $eventId;
        $this->scheduledRestartTime = $scheduledRestartTime;
        $this->timestamp = $timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('server-updates'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'server.restart-pending';
    }
}
