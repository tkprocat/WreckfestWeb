<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\get;

it('can render homepage', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([
            'status' => 'running',
        ], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 1,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'Player1', 'score' => 100],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Test Server');
});

it('displays server status on homepage', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([
            'status' => 'running',
        ], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Server Status');
});

it('displays current players on homepage', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 1,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'Player1', 'score' => 100, 'isBot' => false],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Current Players')
        ->assertSee('Player1')
        ->assertDontSee('(BOT)');
});

it('displays bot players with prefix on homepage', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([], 200),
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

    get('/')
        ->assertSuccessful()
        ->assertSee('RealPlayer')
        ->assertSee('(BOT) BotPlayer');
});

it('shows empty state when no players online', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('No players currently online');
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response(null, 500),
        '*/Server/status' => Http::response(null, 500),
        '*/Server/players' => Http::response(null, 500),
    ]);

    get('/')
        ->assertSuccessful();
});

it('shows login button when not authenticated', function () {
    Http::fake([
        '*/Config/basic' => Http::response(['serverName' => 'Test'], 200),
        '*/Server/status' => Http::response([], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Login');
});

it('shows admin button when authenticated', function () {
    Http::fake([
        '*/Config/basic' => Http::response(['serverName' => 'Test'], 200),
        '*/Server/status' => Http::response([], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ], 200),
    ]);

    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertSuccessful()
        ->assertSee('Admin');
});
