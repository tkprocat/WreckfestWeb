<?php

use App\Filament\Pages\Players;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->user = User::factory()->create();
});

it('can render players page', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 2,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'Player1', 'score' => 100],
                ['name' => 'Player2', 'score' => 50],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user)
        ->get(Players::getUrl())
        ->assertSuccessful();
});

it('displays current players', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 1,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'Player1', 'score' => 100, 'isBot' => false],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user)
        ->get(Players::getUrl())
        ->assertSuccessful()
        ->assertSee('Current Players')
        ->assertSee('Player1')
        ->assertDontSee('(BOT)');
});

it('displays bot players with prefix', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 2,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'RealPlayer', 'score' => 100, 'isBot' => false],
                ['name' => 'BotPlayer', 'score' => 50, 'isBot' => true],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user)
        ->get(Players::getUrl())
        ->assertSuccessful()
        ->assertSee('RealPlayer')
        ->assertSee('(BOT) BotPlayer');
});

it('handles empty players list gracefully', function () {
    Http::fake([
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    actingAs($this->user)
        ->get(Players::getUrl())
        ->assertSuccessful()
        ->assertSee('No players are currently online.');
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Server/players' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(Players::getUrl())
        ->assertSuccessful();
});
