<?php

use App\Filament\Pages\ServerLogs;
use App\Models\User;
use App\Services\WreckfestApiClient;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render server logs page', function () {
    Http::fake([
        '*/Server/logfile*' => Http::response([
            'lines' => 100,
            'source' => 'logfile',
            'logFilePath' => 'C:\\path\\to\\log.txt',
            'output' => [
                'Line 1 of log',
                'Line 2 of log',
                'Line 3 of log',
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerLogs::getUrl())
        ->assertSuccessful();
});

it('displays logs when available', function () {
    Http::fake([
        '*/Server/logfile*' => Http::response([
            'lines' => 100,
            'source' => 'logfile',
            'logFilePath' => 'C:\\path\\to\\log.txt',
            'output' => [
                'Server started',
                'Player connected',
                'Race began',
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerLogs::getUrl())
        ->assertSuccessful()
        ->assertSee('Server Logs');
});

it('handles empty logs gracefully', function () {
    Http::fake([
        '*/Server/logfile*' => Http::response([], 200),
    ]);

    actingAs($this->user)
        ->get(ServerLogs::getUrl())
        ->assertSuccessful()
        ->assertSee('No logs available');
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Server/logfile*' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(ServerLogs::getUrl())
        ->assertSuccessful()
        ->assertSee('No logs available');
});

it('handles string response from API', function () {
    Http::fake([
        '*/Server/logfile*' => Http::response('Log line as string', 200),
    ]);

    actingAs($this->user)
        ->get(ServerLogs::getUrl())
        ->assertSuccessful();
});
