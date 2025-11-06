<?php

use App\Filament\Widgets\CurrentPlayersWidget;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->user = User::factory()->create();
});

it('can render dashboard', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 5,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'SpeedRacer', 'score' => 150, 'isBot' => false],
                ['name' => 'Destructor', 'score' => 120, 'isBot' => true],
                ['name' => 'ProGamer', 'score' => 95, 'isBot' => false],
                ['name' => 'Rampage', 'score' => 80, 'isBot' => true],
                ['name' => 'CrashKing', 'score' => 65, 'isBot' => false],
            ],
            'lastUpdated' => '2025-10-14T12:00:00+02:00',
        ], 200),
    ]);

    $response = actingAs($this->user)
        ->get('/admin');

    $response->assertSuccessful();
});

it('can render current players widget', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 5,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'SpeedRacer', 'score' => 150, 'isBot' => false],
                ['name' => 'Destructor', 'score' => 120, 'isBot' => true],
                ['name' => 'ProGamer', 'score' => 95, 'isBot' => false],
                ['name' => 'Rampage', 'score' => 80, 'isBot' => true],
                ['name' => 'CrashKing', 'score' => 65, 'isBot' => false],
            ],
            'lastUpdated' => '2025-10-14T12:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user);

    $component = Livewire::test(CurrentPlayersWidget::class);

    $component
        ->assertSee('Current Players')
        ->assertSee('SpeedRacer')
        ->assertSee('(BOT) Destructor')
        ->assertSee('ProGamer')
        ->assertSee('(BOT) Rampage')
        ->assertSee('CrashKing')
        ->assertSee('Total Players: 5');
});

it('shows empty state when no players online in widget', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-14T12:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user);

    Livewire::test(CurrentPlayersWidget::class)
        ->assertSee('No players are currently online');
});

it('can refresh players in widget', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 1,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'RefreshTest', 'score' => 100, 'isBot' => false],
            ],
            'lastUpdated' => '2025-10-14T12:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user);

    Livewire::test(CurrentPlayersWidget::class)
        ->assertSee('RefreshTest')
        ->call('refreshPlayers')
        ->assertSee('RefreshTest');
});

it('handles API errors gracefully in widget', function () {
    Http::fake([
        '*/Server/players' => Http::response(null, 500),
    ]);

    actingAs($this->user);

    Livewire::test(CurrentPlayersWidget::class)
        ->assertSuccessful();
});

it('renders bots with orange color class', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 3,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'HumanPlayer', 'score' => 150, 'isBot' => false],
                ['name' => 'BotPlayer1', 'score' => 120, 'isBot' => true],
                ['name' => 'BotPlayer2', 'score' => 80, 'IsBot' => true],
            ],
            'lastUpdated' => '2025-10-14T12:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user);

    $component = Livewire::test(CurrentPlayersWidget::class);

    $html = $component->html();

    // Check if orange color class exists in HTML
    expect($html)->toContain('color: #a03d00;');

    // Check that bot names are formatted correctly
    $component
        ->assertSee('HumanPlayer')
        ->assertSee('(BOT) BotPlayer1')
        ->assertSee('(BOT) BotPlayer2');
});
