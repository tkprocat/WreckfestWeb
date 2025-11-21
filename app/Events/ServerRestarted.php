<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServerRestarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $oldProcessId;

    public int $newProcessId;

    public string $restartMethod;

    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(int $oldProcessId, int $newProcessId, string $restartMethod, string $timestamp)
    {
        $this->oldProcessId = $oldProcessId;
        $this->newProcessId = $newProcessId;
        $this->restartMethod = $restartMethod;
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
        return 'server.restarted';
    }
}
