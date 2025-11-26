<?php

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Models\Event;
use App\Models\TrackCollection;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->user = User::factory()->create();
    $this->collection = TrackCollection::factory()->create([
        'name' => 'Test Collection',
        'tracks' => [
            ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
        ],
    ]);
});

describe('List Events Page', function () {
    it('can render list events page', function () {
        actingAs($this->user)
            ->get(EventResource::getUrl('index'))
            ->assertSuccessful();
    });

    it('displays events in table', function () {
        $event = Event::factory()->create([
            'name' => 'Friday Night Racing',
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->assertCanSeeTableRecords([$event])
            ->assertSee('Friday Night Racing');
    });

    it('can filter active events', function () {
        $activeEvent = Event::factory()->create([
            'name' => 'Active Event',
            'is_active' => true,
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        $inactiveEvent = Event::factory()->create([
            'name' => 'Inactive Event',
            'is_active' => false,
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->assertCanSeeTableRecords([$activeEvent, $inactiveEvent]);
    });

    it('shows activate action for events', function () {
        Http::fake([
            '*/Events/*/activate' => Http::response([], 200),
        ]);

        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->assertTableActionVisible('activate', $event);
    });

    it('can delete event', function () {
        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->callTableAction('delete', $event);

        expect(Event::find($event->id))->toBeNull();
    });
});

describe('Create Event Page', function () {
    it('can render create event page', function () {
        actingAs($this->user)
            ->get(EventResource::getUrl('create'))
            ->assertSuccessful();
    });

    it('can create event with required fields', function () {
        $startTime = now()->addDay();

        Livewire::actingAs($this->user)
            ->test(CreateEvent::class)
            ->fillForm([
                'name' => 'New Event',
                'start_time' => $startTime,
                'track_collection_id' => $this->collection->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('name', 'New Event')->first();

        expect($event)->not->toBeNull()
            ->and($event->name)->toBe('New Event')
            ->and($event->track_collection_id)->toBe($this->collection->id)
            ->and($event->created_by)->toBe($this->user->id);
    });

    it('can create event with description', function () {
        Livewire::actingAs($this->user)
            ->test(CreateEvent::class)
            ->fillForm([
                'name' => 'Event with Description',
                'description' => '<p>This is a test event</p>',
                'start_time' => now()->addDay(),
                'track_collection_id' => $this->collection->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('name', 'Event with Description')->first();

        expect($event->description)->toBe('<p>This is a test event</p>');
    });

    it('can create event with server config', function () {
        Livewire::actingAs($this->user)
            ->test(CreateEvent::class)
            ->fillForm([
                'name' => 'Event with Config',
                'start_time' => now()->addDay(),
                'track_collection_id' => $this->collection->id,
                'server_config' => [
                    'serverName' => 'Special Event Server',
                    'welcomeMessage' => 'Welcome to the event!',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('name', 'Event with Config')->first();

        expect($event->server_config)->toMatchArray([
            'serverName' => 'Special Event Server',
            'welcomeMessage' => 'Welcome to the event!',
        ]);
    });

    it('can create event with repeat pattern', function () {
        Livewire::actingAs($this->user)
            ->test(CreateEvent::class)
            ->fillForm([
                'name' => 'Weekly Event',
                'start_time' => now()->addDay(),
                'track_collection_id' => $this->collection->id,
                'repeat' => [
                    'frequency' => 'weekly',
                    'days' => [1, 3, 5], // Monday, Wednesday, Friday
                    'time' => '20:00',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('name', 'Weekly Event')->first();

        expect($event->repeat)->toBe([
            'frequency' => 'weekly',
            'days' => [1, 3, 5],
            'time' => '20:00',
        ]);
    });

    it('validates required fields', function () {
        Livewire::actingAs($this->user)
            ->test(CreateEvent::class)
            ->fillForm([
                'name' => '',
                'start_time' => null,
                'track_collection_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'start_time', 'track_collection_id']);
    });
});

describe('Edit Event Page', function () {
    it('can render edit event page', function () {
        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        actingAs($this->user)
            ->get(EventResource::getUrl('edit', ['record' => $event]))
            ->assertSuccessful();
    });

    it('can retrieve event data', function () {
        $event = Event::factory()->create([
            'name' => 'Existing Event',
            'description' => '<p>Event Description</p>',
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->assertFormSet([
                'name' => 'Existing Event',
                'description' => '<p>Event Description</p>',
                'track_collection_id' => $this->collection->id,
            ]);
    });

    it('can update event', function () {
        $event = Event::factory()->create([
            'name' => 'Old Name',
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm([
                'name' => 'New Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($event->fresh()->name)->toBe('New Name');
    });

    it('can update server config', function () {
        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
            'server_config' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm([
                'server_config' => [
                    'serverName' => 'Updated Server Name',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($event->fresh()->server_config['serverName'])->toBe('Updated Server Name');
    });
});

describe('Event Activation', function () {
    it('can activate event via action', function () {
        Http::fake([
            '*/Events/*/activate' => Http::response([], 200),
        ]);

        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->callTableAction('activate', $event);

        Http::assertSent(function ($request) use ($event) {
            return str_contains($request->url(), "/Events/{$event->id}/activate");
        });
    });

    it('shows success notification on successful activation', function () {
        Http::fake([
            '*/Events/*/activate' => Http::response([], 200),
        ]);

        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->callTableAction('activate', $event)
            ->assertNotified();
    });

    it('shows error notification on failed activation', function () {
        Http::fake([
            '*/Events/*/activate' => Http::response([], 500),
        ]);

        $event = Event::factory()->create([
            'track_collection_id' => $this->collection->id,
            'created_by' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ListEvents::class)
            ->callTableAction('activate', $event)
            ->assertNotified();
    });
});
