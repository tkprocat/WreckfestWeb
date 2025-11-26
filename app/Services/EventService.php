<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function __construct(
        protected WreckfestApiClient $apiClient
    ) {}

    /**
     * Build the event schedule for the Wreckfest Controller
     * Returns array of events with all necessary data
     */
    public function buildEventSchedule(): array
    {
        // Get all upcoming, active, and recurring events, ordered by start_time
        $events = Event::with(['trackCollection', 'creator'])
            ->where(function ($query) {
                $query->where('is_active', true)
                    ->orWhere('start_time', '>=', now())
                    ->orWhereNotNull('recurring_pattern'); // Include recurring events even if start_time is past
            })
            ->orderBy('start_time')
            ->get();

        return $events->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'startTime' => $event->start_time->utc()->toIso8601ZuluString(), // Always send UTC with Z suffix
                'isActive' => $event->is_active,
                'serverConfig' => $event->server_config,
                'tracks' => $event->trackCollection?->tracks ?? [],
                'collectionName' => $event->trackCollection?->name,
                'recurringPattern' => $event->recurring_pattern,
            ];
        })->toArray();
    }

    /**
     * Push the complete event schedule to the Wreckfest Controller
     */
    public function pushScheduleToController(): bool
    {
        try {
            $schedule = $this->buildEventSchedule();

            Log::info('Pushing event schedule to controller', [
                'event_count' => count($schedule)
            ]);

            return $this->apiClient->pushEventSchedule($schedule);
        } catch (\Exception $e) {
            Log::error('Failed to push event schedule to controller: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get upcoming events for frontend display
     */
    public function getUpcomingEvents(int $limit = 5): array
    {
        return Event::with('trackCollection')
            ->where('start_time', '>=', now())
            ->where('is_active', false)
            ->orderBy('start_time')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get the currently active event, if any
     */
    public function getActiveEvent(): ?Event
    {
        return Event::with('trackCollection')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Mark an event as activated
     */
    public function activateEvent(int $eventId): bool
    {
        try {
            // Deactivate all other events first
            Event::where('is_active', true)->update(['is_active' => false]);

            // Activate this event
            $event = Event::findOrFail($eventId);
            $event->is_active = true;
            $event->save();

            Log::info('Event activated', ['event_id' => $eventId, 'event_name' => $event->name]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to activate event: ' . $e->getMessage());
            return false;
        }
    }
}
