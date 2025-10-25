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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Server Online');
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSeeText('No players currently')
        ->assertSeeText('Be the first to join!');
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response(null, 500),
        '*/Server/status' => Http::response(null, 500),
        '*/Server/players' => Http::response(null, 500),
        '*/Config/tracks' => Http::response(null, 500),
        '*/Config/tracks/collection-name' => Http::response(null, 500),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Wreckfest Server');
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
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
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
        ], 200),
    ]);

    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertSuccessful()
        ->assertSee('Admin');
});

it('displays track rotation on homepage', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([
            'currentTrack' => 'speedway2_inner_oval',
        ], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
        ], 200),
        '*/Config/tracks' => Http::response([
            'count' => 2,
            'tracks' => [
                [
                    'track' => 'speedway2_inner_oval',
                    'gamemode' => 'racing',
                    'laps' => 5,
                    'bots' => 8,
                ],
                [
                    'track' => 'sandstone_stadium_a',
                    'gamemode' => 'derby',
                    'laps' => 3,
                    'bots' => 12,
                ],
            ],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => 'Test Collection',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Track Rotation')
        ->assertSee('Test Collection')
        ->assertSee('Big Valley Speedway')
        ->assertSee('Inner Oval')
        ->assertSee('NOW PLAYING')
        ->assertSee('Racing')
        ->assertSee('5 Laps')
        ->assertSee('8 Bots');
});

it('shows empty state when no track rotation configured', function () {
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
        ], 200),
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => null,
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Track Rotation')
        ->assertSee('No track rotation configured');
});

it('highlights current track in rotation', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Server/status' => Http::response([
            'currentTrack' => 'sandstone_stadium_a',
        ], 200),
        '*/Server/players' => Http::response([
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
        ], 200),
        '*/Config/tracks' => Http::response([
            'count' => 2,
            'tracks' => [
                [
                    'track' => 'speedway2_inner_oval',
                    'gamemode' => 'racing',
                    'laps' => 5,
                ],
                [
                    'track' => 'sandstone_stadium_a',
                    'gamemode' => 'derby',
                    'laps' => 3,
                ],
            ],
        ], 200),
        '*/Config/tracks/collection-name' => Http::response([
            'collectionName' => 'Test Collection',
        ], 200),
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('NOW PLAYING');
});
