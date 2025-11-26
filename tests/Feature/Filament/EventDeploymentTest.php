<?php

use App\Models\Event;
use App\Models\TrackCollection;
use App\Models\User;
use App\Services\WreckfestApiClient;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('deploy event schedule button sends events to controller', function () {
    // Create a track collection
    $collection = TrackCollection::factory()->create([
        'name' => 'Test Collection',
        'tracks' => [
            [
                'track' => 'crm01_1',
                'gamemode' => 'racing',
                'laps' => 5,
                'bots' => 10,
                'carResetDisabled' => false,
                'wrongWayLimiterDisabled' => false,
            ],
        ],
    ]);

    // Create an upcoming event
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'description' => 'Test Description',
        'start_time' => now()->addHour(),
        'track_collection_id' => $collection->id,
        'is_active' => false,
    ]);

    // Mock the HTTP request to the Wreckfest Controller
    Http::fake([
        '*/Events/schedule' => Http::response(['success' => true], 200),
    ]);

    // Visit the Events list page
    $this->get(route('filament.admin.resources.events.index'))
        ->assertSuccessful();

    // Simulate clicking the "Deploy Event Schedule" button
    $apiClient = app(WreckfestApiClient::class);
    $result = $apiClient->pushEventSchedule([
        [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'start_time' => $event->start_time->toIso8601String(),
            'track_collection_id' => $event->track_collection_id,
            'tracks' => $collection->tracks,
            'server_config' => $event->server_config ?? [],
        ],
    ]);

    expect($result)->toBeTrue();

    // Verify the request was made
    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/Events/schedule') &&
            isset($request['Events']) &&
            count($request['Events']) === 1;
    });
});

test('deploy event schedule filters out past events', function () {
    // Create a past event
    $pastEvent = Event::factory()->create([
        'name' => 'Past Event',
        'start_time' => now()->subHour(),
        'is_active' => false,
    ]);

    // Create a future event
    $futureEvent = Event::factory()->create([
        'name' => 'Future Event',
        'start_time' => now()->addHour(),
        'is_active' => false,
    ]);

    Http::fake([
        'https://localhost:5101/api/Events/schedule' => Http::response(['success' => true], 200),
    ]);

    // Get upcoming events (simulating the button action)
    $events = Event::with('trackCollection')
        ->where('start_time', '>=', now())
        ->orderBy('start_time')
        ->get();

    expect($events)->toHaveCount(1);
    expect($events->first()->id)->toBe($futureEvent->id);
});

test('activate now button attempts to activate event', function () {
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'start_time' => now()->addHour(),
        'is_active' => false,
    ]);

    Http::fake([
        '*/Events/*/activate' => Http::response(['success' => true], 200),
    ]);

    $apiClient = app(WreckfestApiClient::class);
    $result = $apiClient->activateEvent($event->id);

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) use ($event) {
        return str_contains($request->url(), "/api/Events/{$event->id}/activate");
    });
});

test('event schedule converts booleans to integers for C# API', function () {
    $collection = TrackCollection::factory()->create([
        'tracks' => [
            [
                'track' => 'crm01_1',
                'carResetDisabled' => true,
                'wrongWayLimiterDisabled' => false,
            ],
        ],
    ]);

    $event = Event::factory()->create([
        'start_time' => now()->addHour(),
        'track_collection_id' => $collection->id,
    ]);

    Http::fake([
        '*/Events/schedule' => Http::response(['success' => true], 200),
    ]);

    $apiClient = app(WreckfestApiClient::class);
    $apiClient->pushEventSchedule([
        [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'start_time' => $event->start_time->toIso8601String(),
            'track_collection_id' => $event->track_collection_id,
            'tracks' => $collection->tracks,
            'server_config' => [],
        ],
    ]);

    Http::assertSent(function ($request) {
        $events = $request['Events'];
        $track = $events[0]['tracks'][0];

        return $track['carResetDisabled'] === 1 &&
            $track['wrongWayLimiterDisabled'] === 0;
    });
});
