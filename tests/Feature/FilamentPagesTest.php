<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Http::preventStrayRequests();

    // Create a test user for authentication
    $this->user = \App\Models\User::factory()->create();

    // Mock the API responses
    config(['wreckfest.api_url' => 'https://localhost:5101/api']);
});

describe('Server Config Page', function () {
    test('can access server config page when authenticated', function () {
        Http::fake([
            '*/Config/basic' => Http::response([
                'serverName' => 'Test Server',
                'maxPlayers' => 24,
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-config');

        $response->assertSuccessful();
    });

    test('cannot access server config page when not authenticated', function () {
        $response = get('/admin/server-config');

        $response->assertRedirect('/admin/login');
    });

    test('displays server configuration data', function () {
        Http::fake([
            '*/Config/basic' => Http::response([
                'serverName' => 'Test Server',
                'maxPlayers' => 24,
                'welcomeMessage' => 'Welcome to my server!',
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-config');

        $response->assertSuccessful()
            ->assertSee('Test Server')
            ->assertSee('24');
    });
});

describe('Track Rotation Page', function () {
    test('can access track rotation page when authenticated', function () {
        Http::fake([
            '*/Config/tracks' => Http::response([
                'count' => 2,
                'tracks' => [
                    'count' => 1,
                    'tracks' => [
                        ['track' => 'Track 1', 'gamemode' => 'Race', 'laps' => 5],
                    ],
                ],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/track-rotation');

        $response->assertSuccessful();
    });

    test('cannot access track rotation page when not authenticated', function () {
        $response = get('/admin/track-rotation');

        $response->assertRedirect('/admin/login');
    });

    test('displays track rotation data', function () {
        Http::fake([
            '*Config/basic*' => Http::response([
                'serverName' => 'Test Server',
                'maxPlayers' => 24,
                'welcomeMessage' => 'Welcome!',
            ], 200),
            '*/Config/tracks' => Http::response([
                'count' => 2,
                'tracks' => [
                    'count' => 2,
                    'tracks' => [
                        ['track' => 'Sandpit', 'gamemode' => 'Race', 'laps' => 5],
                        ['track' => 'Banger Racing', 'gamemode' => 'Derby', 'laps' => 3],
                    ],
                ],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/track-rotation');

        $response->assertSuccessful()
            ->assertSee('Sandpit')
            ->assertSee('Banger Racing');
    });
});

describe('Server Control Page', function () {
    test('can access server control page when authenticated', function () {
        Http::fake([
            '*/Server/status' => Http::response([
                'running' => true,
                'playerCount' => 10,
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-control');

        $response->assertSuccessful();
    });

    test('cannot access server control page when not authenticated', function () {
        $response = get('/admin/server-control');

        $response->assertRedirect('/admin/login');
    });

    test('displays server status', function () {
        Http::fake([
            '*/Server/status' => Http::response([
                'running' => true,
                'playerCount' => 10,
                'serverName' => 'Test Server',
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-control');

        $response->assertSuccessful()
            ->assertSee('Server Status');
    });
});

describe('Server Logs Page', function () {
    test('can access server logs page when authenticated', function () {
        Http::fake([
            '*/Server/logfile*' => Http::response([
                'lines' => 100,
                'source' => 'logfile',
                'logFilePath' => 'C:\\path\\to\\log.txt',
                'output' => [
                    'Server started',
                    'Player joined',
                    'Race started',
                ],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-logs');

        $response->assertSuccessful();
    });

    test('cannot access server logs page when not authenticated', function () {
        $response = get('/admin/server-logs');

        $response->assertRedirect('/admin/login');
    });

    test('displays server logs', function () {
        Http::fake([
            '*/Server/logfile*' => Http::response([
                'lines' => 100,
                'source' => 'logfile',
                'logFilePath' => 'C:\\path\\to\\log.txt',
                'output' => [
                    'Server started at 10:00',
                    'Player1 joined the game',
                ],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-logs');

        $response->assertSuccessful()
            ->assertSee('Server Logs');
    });
});

describe('Players Page', function () {
    test('can access players page when authenticated', function () {
        Http::fake([
            '*/Server/players' => Http::response([
                ['name' => 'Player1', 'score' => 100],
                ['name' => 'Player2', 'score' => 80],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/players');

        $response->assertSuccessful();
    });

    test('cannot access players page when not authenticated', function () {
        $response = get('/admin/players');

        $response->assertRedirect('/admin/login');
    });

    test('displays current players', function () {
        Http::fake([
            '*/Server/players' => Http::response([
                ['name' => 'TestPlayer1', 'score' => 100],
                ['name' => 'TestPlayer2', 'score' => 80],
            ], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/players');

        $response->assertSuccessful()
            ->assertSee('Current Players');
    });

    test('shows message when no players online', function () {
        Http::fake([
            '*/Server/players' => Http::response([], 200),
        ]);

        actingAs($this->user);

        $response = get('/admin/players');

        $response->assertSuccessful()
            ->assertSee('No players are currently online.');
    });
});

describe('API Error Handling', function () {
    test('handles API failures gracefully on server config page', function () {
        Http::fake([
            '*/Config/basic' => Http::response(null, 500),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-config');

        $response->assertSuccessful(); // Page should still load
    });

    test('handles API failures gracefully on track rotation page', function () {
        Http::fake([
            '*/Config/tracks' => Http::response(null, 500),
        ]);

        actingAs($this->user);

        $response = get('/admin/track-rotation');

        $response->assertSuccessful(); // Page should still load
    });

    test('handles API failures gracefully on server control page', function () {
        Http::fake([
            '*/Server/status' => Http::response(null, 500),
        ]);

        actingAs($this->user);

        $response = get('/admin/server-control');

        $response->assertSuccessful(); // Page should still load
    });
});
