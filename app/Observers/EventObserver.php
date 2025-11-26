<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Support\Facades\Log;

class EventObserver
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->pushSchedule('created', $event);
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        $this->pushSchedule('updated', $event);
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        $this->pushSchedule('deleted', $event);
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        $this->pushSchedule('restored', $event);
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        $this->pushSchedule('forceDeleted', $event);
    }

    /**
     * Push the updated schedule to the Wreckfest Controller
     */
    protected function pushSchedule(string $action, Event $event): void
    {
        try {
            Log::info("Event {$action}, pushing schedule to controller", [
                'event_id' => $event->id,
                'event_name' => $event->name,
            ]);

            $this->eventService->pushScheduleToController();
        } catch (\Exception $e) {
            Log::error("Failed to push schedule after event {$action}: " . $e->getMessage());
        }
    }
}
