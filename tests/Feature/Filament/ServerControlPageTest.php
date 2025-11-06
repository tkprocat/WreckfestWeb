<?php

use App\Filament\Pages\ServerControl;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->user = User::factory()->create();
});

it('can render server control page', function () {
    Http::fake([
        '*/Server/status' => Http::response([
            'status' => 'running',
            'players' => 5,
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerControl::getUrl())
        ->assertSuccessful();
});

it('displays server status', function () {
    Http::fake([
        '*/Server/status' => Http::response([
            'status' => 'running',
            'players' => 5,
        ], 200),
    ]);

    actingAs($this->user)
        ->get(ServerControl::getUrl())
        ->assertSuccessful()
        ->assertSee('Server Control');
});

it('handles empty status gracefully', function () {
    Http::fake([
        '*/Server/status' => Http::response([], 200),
    ]);

    actingAs($this->user)
        ->get(ServerControl::getUrl())
        ->assertSuccessful();
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Server/status' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(ServerControl::getUrl())
        ->assertSuccessful();
});
