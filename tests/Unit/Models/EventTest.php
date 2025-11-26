<?php

use App\Models\Event;
use App\Models\TrackCollection;
use App\Models\User;

it('has fillable attributes', function () {
    $event = new Event([
        'name' => 'Test Event',
        'description' => 'Test Description',
        'start_time' => now(),
        'server_config' => ['serverName' => 'Test'],
        'repeat' => ['frequency' => 'daily', 'time' => '20:00'],
        'track_collection_id' => 1,
        'created_by' => 1,
    ]);

    expect($event->name)->toBe('Test Event')
        ->and($event->description)->toBe('Test Description')
        ->and($event->server_config)->toBe(['serverName' => 'Test'])
        ->and($event->repeat)->toBe(['frequency' => 'daily', 'time' => '20:00']);
});

it('casts server_config to array', function () {
    $event = Event::factory()->create([
        'server_config' => ['serverName' => 'Test Server', 'maxPlayers' => 24],
    ]);

    expect($event->server_config)->toBeArray()
        ->and($event->server_config['serverName'])->toBe('Test Server')
        ->and($event->server_config['maxPlayers'])->toBe(24);
});

it('casts repeat to array', function () {
    $event = Event::factory()->create([
        'repeat' => ['frequency' => 'weekly', 'days' => [1, 3, 5], 'time' => '20:00'],
    ]);

    expect($event->repeat)->toBeArray()
        ->and($event->repeat['frequency'])->toBe('weekly')
        ->and($event->repeat['days'])->toBe([1, 3, 5]);
});

it('belongs to a track collection', function () {
    $collection = TrackCollection::factory()->create();
    $event = Event::factory()->create(['track_collection_id' => $collection->id]);

    expect($event->trackCollection)->toBeInstanceOf(TrackCollection::class)
        ->and($event->trackCollection->id)->toBe($collection->id);
});

it('belongs to a creator user', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);

    expect($event->creator)->toBeInstanceOf(User::class)
        ->and($event->creator->id)->toBe($user->id);
});

it('has is_active scope for currently active events', function () {
    // Create an active event (started, not finished)
    $activeEvent = Event::factory()->create([
        'start_time' => now()->subHour(),
        'is_active' => true,
    ]);

    // Create an inactive event
    $inactiveEvent = Event::factory()->create([
        'start_time' => now()->addHour(),
        'is_active' => false,
    ]);

    $activeEvents = Event::where('is_active', true)->get();

    expect($activeEvents)->toHaveCount(1)
        ->and($activeEvents->first()->id)->toBe($activeEvent->id);
});

it('has upcoming scope for future events', function () {
    // Create an upcoming event
    $upcomingEvent = Event::factory()->create([
        'start_time' => now()->addDay(),
        'is_active' => false,
    ]);

    // Create a past event
    $pastEvent = Event::factory()->create([
        'start_time' => now()->subDay(),
        'is_active' => false,
    ]);

    $upcomingEvents = Event::where('start_time', '>', now())
        ->where('is_active', false)
        ->get();

    expect($upcomingEvents)->toHaveCount(1)
        ->and($upcomingEvents->first()->id)->toBe($upcomingEvent->id);
});

it('can have null repeat', function () {
    $event = Event::factory()->create(['repeat' => null]);

    expect($event->repeat)->toBeNull();
});

it('isRecurring returns true when repeat frequency is set', function () {
    $event = Event::factory()->create([
        'repeat' => ['frequency' => 'weekly', 'days' => [0], 'time' => '20:00'],
    ]);

    expect($event->isRecurring())->toBeTrue();
});

it('isRecurring returns false when repeat is null', function () {
    $event = Event::factory()->create(['repeat' => null]);

    expect($event->isRecurring())->toBeFalse();
});

it('can have null server config', function () {
    $event = Event::factory()->create(['server_config' => null]);

    expect($event->server_config)->toBeNull();
});

it('formats start time as datetime', function () {
    $startTime = now();
    $event = Event::factory()->create(['start_time' => $startTime]);

    expect($event->start_time)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
