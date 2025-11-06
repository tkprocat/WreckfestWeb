<?php

use App\Filament\Pages\ConfigPreview;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->user = User::factory()->create();
});

it('can render config preview page', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Config/tracks' => Http::response([
            'count' => 1,
            'tracks' => [
                ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ConfigPreview::getUrl())
        ->assertSuccessful();
});

it('displays server configuration preview', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ConfigPreview::getUrl())
        ->assertSuccessful()
        ->assertSee('server_name=Test Server')
        ->assertSee('max_players=24');
});

it('displays track rotation preview', function () {
    Http::fake([
        '*/Config/basic' => Http::response([], 200),
        '*/Config/tracks' => Http::response([
            'count' => 1,
            'tracks' => [
                ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ConfigPreview::getUrl())
        ->assertSuccessful()
        ->assertSee('el_add=loop')
        ->assertSee('el_laps=5');
});

it('handles empty config and tracks gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response([], 200),
        '*/Config/tracks' => Http::response([
            'count' => 0,
            'tracks' => [],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ConfigPreview::getUrl())
        ->assertSuccessful()
        ->assertSee('server_config.cfg');
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response(null, 500),
        '*/Config/tracks' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(ConfigPreview::getUrl())
        ->assertSuccessful();
});
