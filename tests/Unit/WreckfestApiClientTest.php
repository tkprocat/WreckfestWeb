<?php

use App\Exceptions\WreckfestApiException;
use App\Services\WreckfestApiClient;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['wreckfest.api_url' => 'https://localhost:5101/api']);
    $this->client = new WreckfestApiClient();
});

describe('Server Configuration', function () {
    test('can get server configuration', function () {
        Http::fake([
            '*/Config/basic' => Http::response([
                'serverName' => 'Test Server',
                'maxPlayers' => 24,
                'welcomeMessage' => 'Welcome!',
            ], 200),
        ]);

        $config = $this->client->getServerConfig();

        expect($config)->toBeArray()
            ->and($config)->toHaveKey('serverName')
            ->and($config['serverName'])->toBe('Test Server')
            ->and($config['maxPlayers'])->toBe(24);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://localhost:5101/api/Config/basic';
        });
    });

    test('handles failed get server configuration gracefully', function () {
        Http::fake([
            '*/Config/basic' => Http::response(null, 500),
        ]);

        $config = $this->client->getServerConfig();

        expect($config)->toBeArray()
            ->and($config)->toBeEmpty();
    });

    test('can update server configuration', function () {
        Http::fake([
            '*/Config/basic' => Http::response(null, 200),
        ]);

        $result = $this->client->updateServerConfig([
            'serverName' => 'Updated Server',
            'maxPlayers' => 32,
        ]);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT' &&
                   $request->url() === 'https://localhost:5101/api/Config/basic' &&
                   $request->data()['serverName'] === 'Updated Server';
        });
    });

    test('returns false when update server configuration fails', function () {
        Http::fake([
            '*/Config/basic' => Http::response(null, 500),
        ]);

        $result = $this->client->updateServerConfig(['serverName' => 'Test']);

        expect($result)->toBeFalse();
    });
});

describe('Track Management', function () {
    test('can get tracks', function () {
        // Mock the API response structure that Wreckfest API actually returns
        $apiResponse = [
            'count' => 2,
            'tracks' => [
                ['track' => 'Track 1', 'gamemode' => 'Race', 'laps' => 5],
                ['track' => 'Track 2', 'gamemode' => 'Derby', 'laps' => 3],
            ],
        ];

        Http::fake([
            '*/Config/tracks' => Http::response($apiResponse, 200),
        ]);

        $result = $this->client->getTracks();

        expect($result)->toBeArray()
            ->and($result)->toHaveCount(2)
            ->and($result[0]['track'])->toBe('Track 1')
            ->and($result[1]['track'])->toBe('Track 2');
    });

    test('can update tracks', function () {
        Http::fake([
            '*/Config/tracks' => Http::response(null, 200),
        ]);

        $tracks = [
            ['track' => 'New Track', 'gamemode' => 'Race'],
        ];

        $result = $this->client->updateTracks($tracks);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT' &&
                   str_contains($request->url(), '/Config/tracks');
        });
    });

    test('can add a track', function () {
        Http::fake([
            '*/Config/tracks' => Http::response(null, 200),
        ]);

        $result = $this->client->addTrack([
            'track' => 'New Track',
            'gamemode' => 'Race',
            'laps' => 5,
        ]);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), '/Config/tracks');
        });
    });

    test('can update a specific track', function () {
        Http::fake([
            '*/Config/tracks/*' => Http::response(null, 200),
        ]);

        $result = $this->client->updateTrack(0, [
            'track' => 'Updated Track',
            'gamemode' => 'Derby',
        ]);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT' &&
                   str_contains($request->url(), '/Config/tracks/0');
        });
    });

    test('can delete a track', function () {
        Http::fake([
            '*/Config/tracks/*' => Http::response(null, 200),
        ]);

        $result = $this->client->deleteTrack(0);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE' &&
                   str_contains($request->url(), '/Config/tracks/0');
        });
    });
});

describe('Server Control', function () {
    test('can get server status', function () {
        Http::fake([
            '*/Server/status' => Http::response([
                'running' => true,
                'playerCount' => 10,
            ], 200),
        ]);

        $status = $this->client->getServerStatus();

        expect($status)->toBeArray()
            ->and($status)->toHaveKey('running')
            ->and($status['running'])->toBeTrue();
    });

    test('can start server', function () {
        Http::fake([
            '*/Server/start' => Http::response(null, 200),
        ]);

        $result = $this->client->startServer();

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), '/Server/start');
        });
    });

    test('can stop server', function () {
        Http::fake([
            '*/Server/stop' => Http::response(null, 200),
        ]);

        $result = $this->client->stopServer();

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), '/Server/stop');
        });
    });

    test('can restart server', function () {
        Http::fake([
            '*/Server/restart' => Http::response(null, 200),
        ]);

        $result = $this->client->restartServer();

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), '/Server/restart');
        });
    });

    test('can attach to server process', function () {
        Http::fake([
            '*/Server/attach/*' => Http::response(null, 200),
        ]);

        $result = $this->client->attachToServer(1234);

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), '/Server/attach/1234');
        });
    });
});

describe('Server Monitoring', function () {
    test('can get log file', function () {
        // Mock the nested API response structure that Wreckfest API actually returns
        $apiResponse = [
            'lines' => 100,
            'source' => 'logfile',
            'logFilePath' => 'C:\\path\\to\\log.txt',
            'output' => [
                'Log line 1',
                'Log line 2',
                'Log line 3',
            ],
        ];

        Http::fake([
            '*/Server/logfile*' => Http::response($apiResponse, 200),
        ]);

        $result = $this->client->getLogFile(100);

        expect($result)->toBeArray()
            ->and($result)->toHaveCount(3)
            ->and($result[0])->toBe('Log line 1')
            ->and($result[2])->toBe('Log line 3');

        Http::assertSent(function ($request) {
            return $request->method() === 'GET' &&
                   str_contains($request->url(), '/Server/logfile') &&
                   str_contains($request->url(), 'lines=100');
        });
    });

    test('uses default line count when not specified', function () {
        Http::fake([
            '*/Server/logfile*' => Http::response([], 200),
        ]);

        $this->client->getLogFile();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'lines=100');
        });
    });

    test('can get players', function () {
        // Mock the nested API response structure that Wreckfest API actually returns
        $apiResponse = [
            'totalPlayers' => 2,
            'maxPlayers' => 24,
            'players' => [
                ['name' => 'Player 1', 'score' => 100],
                ['name' => 'Player 2', 'score' => 80],
            ],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ];

        Http::fake([
            '*/Server/players' => Http::response($apiResponse, 200),
        ]);

        $result = $this->client->getPlayers();

        expect($result)->toBeArray()
            ->and($result)->toHaveCount(2)
            ->and($result[0]['name'])->toBe('Player 1')
            ->and($result[1]['name'])->toBe('Player 2');
    });

    test('handles empty player list', function () {
        // Mock empty players with nested structure
        $apiResponse = [
            'totalPlayers' => 0,
            'maxPlayers' => 24,
            'players' => [],
            'lastUpdated' => '2025-10-13T20:00:00+02:00',
        ];

        Http::fake([
            '*/Server/players' => Http::response($apiResponse, 200),
        ]);

        $result = $this->client->getPlayers();

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });
});

describe('Error Handling', function () {
    test('handles network exceptions gracefully', function () {
        Http::fake(function () {
            throw new Exception('Network error');
        });

        expect(fn() => $this->client->getServerConfig())->toThrow(WreckfestApiException::class);
        expect(fn() => $this->client->getTracks())->toThrow(WreckfestApiException::class);
        expect(fn() => $this->client->getServerStatus())->toThrow(WreckfestApiException::class);
    });

    test('handles API errors gracefully', function () {
        Http::fake([
            '*' => Http::response(null, 404),
        ]);

        $result = $this->client->startServer();

        expect($result)->toBeFalse();
    });
});
