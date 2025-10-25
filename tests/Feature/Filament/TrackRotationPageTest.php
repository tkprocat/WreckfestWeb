<?php

use App\Filament\Pages\TrackRotation;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render track rotation page', function () {
    Http::fake([
        '*/Config/tracks' => Http::response([
            'count' => 2,
            'tracks' => [
                'count' => 2,
                'tracks' => [
                    ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
                    ['track' => 'speedway2_figure_8', 'gamemode' => 'derby', 'laps' => 3],
                ],
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(TrackRotation::getUrl())
        ->assertSuccessful()
        ->assertSee('Loop') // Track name from config
        ->assertSee('Big Valley Speedway - Figure 8'); // Track name from config
});

it('displays track rotation', function () {
    Http::fake([
        '*/Config/tracks' => Http::response([
            'count' => 2,
            'tracks' => [
                'count' => 1,
                'tracks' => [
                    ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
                ],
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(TrackRotation::getUrl())
        ->assertSuccessful()
        ->assertSee('Track Rotation')
        ->assertSee('Loop') // Verify the track name is displayed
        ->assertSee('5'); // Verify laps are displayed
});

it('handles empty tracks gracefully', function () {
    Http::fake([
        '*/Config/tracks' => Http::response([
            'count' => 2,
            'tracks' => [
                'count' => 0,
                'tracks' => [],
            ],
        ], 200),
    ]);

    actingAs($this->user)
        ->get(TrackRotation::getUrl())
        ->assertSuccessful();
});

it('handles API errors gracefully', function () {
    Http::fake([
        '*/Config/tracks' => Http::response(null, 500),
    ]);

    actingAs($this->user)
        ->get(TrackRotation::getUrl())
        ->assertSuccessful();
});
