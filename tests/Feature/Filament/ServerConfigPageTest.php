<?php

use App\Filament\Pages\ServerConfig;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render server config page', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
            'welcomeMessage' => 'Welcome!',
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerConfig::getUrl())
        ->assertSuccessful();
});

it('displays server configuration', function () {
    Http::fake([
        '*/Config/basic' => Http::response([
            'serverName' => 'Test Server',
            'maxPlayers' => 24,
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerConfig::getUrl())
        ->assertSuccessful()
        ->assertSee('Server Config');
});

it('handles empty config gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response([], 200),
    ]);

    actingAs($this->user)
        ->get(ServerConfig::getUrl())
        ->assertSuccessful();
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Config/basic' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(ServerConfig::getUrl())
        ->assertSuccessful();
});
