<?php

namespace App\Livewire;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class UpcomingEvents extends Component
{
    public $events = [];

    public $activeEvent = null;

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        // Get upcoming events
        $this->events = Event::with('trackCollection')
            ->where('start_time', '>=', now())
            ->where('is_active', false)
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'start_time' => $event->start_time,
                    'track_collection_name' => $event->trackCollection?->name,
                    'track_count' => count($event->trackCollection?->tracks ?? []),
                    'tracks' => $event->trackCollection?->tracks ?? [],
                ];
            })
            ->toArray();

        // Get currently active event
        $activeEvent = Event::with('trackCollection')
            ->where('is_active', true)
            ->first();

        if ($activeEvent) {
            $this->activeEvent = [
                'id' => $activeEvent->id,
                'name' => $activeEvent->name,
                'description' => $activeEvent->description,
                'track_collection_name' => $activeEvent->trackCollection?->name,
                'tracks' => $activeEvent->trackCollection?->tracks ?? [],
            ];
        } else {
            $this->activeEvent = null;
        }
    }

    #[On('echo:server-updates,.event.activated')]
    public function eventActivated($event)
    {
        Log::info('Livewire UpcomingEvents: Received event.activated', $event);

        // Reload events to reflect the change
        $this->loadEvents();
    }

    public function placeholder()
    {
        return view('livewire.upcoming-events-placeholder');
    }

    public function render()
    {
        return view('livewire.upcoming-events');
    }
}
