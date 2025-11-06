<?php

use App\Models\Track;
use App\Models\TrackVariant;
use App\Models\WeatherCondition;

it('has fillable attributes', function () {
    $track = new Track([
        'key' => 'test_track',
        'name' => 'Test Track',
    ]);

    expect($track->key)->toBe('test_track')
        ->and($track->name)->toBe('Test Track');
});

it('has many variants', function () {
    $track = Track::factory()->create();
    $variant1 = TrackVariant::factory()->create(['track_id' => $track->id]);
    $variant2 = TrackVariant::factory()->create(['track_id' => $track->id]);

    expect($track->variants)->toHaveCount(2)
        ->and($track->variants->first()->id)->toBe($variant1->id)
        ->and($track->variants->last()->id)->toBe($variant2->id);
});

it('belongs to many weather conditions', function () {
    $track = Track::factory()->create();
    $clear = WeatherCondition::firstOrCreate(['name' => 'clear']);
    $overcast = WeatherCondition::firstOrCreate(['name' => 'overcast']);

    $track->weatherConditions()->attach([$clear->id, $overcast->id]);

    expect($track->weatherConditions)->toHaveCount(2)
        ->and($track->weatherConditions->pluck('name')->toArray())->toContain('clear')
        ->and($track->weatherConditions->pluck('name')->toArray())->toContain('overcast');
});

it('can check if track supports specific weather', function () {
    $track = Track::factory()->create();
    $clear = WeatherCondition::firstOrCreate(['name' => 'clear']);
    $overcast = WeatherCondition::firstOrCreate(['name' => 'overcast']);
    $rain = WeatherCondition::firstOrCreate(['name' => 'rain']);

    $track->weatherConditions()->attach([$clear->id, $overcast->id]);

    expect($track->supportsWeather('clear'))->toBeTrue()
        ->and($track->supportsWeather('overcast'))->toBeTrue()
        ->and($track->supportsWeather('rain'))->toBeFalse()
        ->and($track->supportsWeather('storm'))->toBeFalse();
});

it('has unique key', function () {
    Track::factory()->create(['key' => 'unique_track']);

    expect(function () {
        Track::factory()->create(['key' => 'unique_track']);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

it('supports tracks with limited weather conditions', function () {
    // Test a track that only supports 'clear' weather (like Sandstone Raceway)
    $track = Track::factory()->create(['key' => 'sandstone_raceway', 'name' => 'Sandstone Raceway']);
    $clear = WeatherCondition::firstOrCreate(['name' => 'clear']);

    $track->weatherConditions()->attach($clear->id);

    expect($track->weatherConditions)->toHaveCount(1)
        ->and($track->supportsWeather('clear'))->toBeTrue()
        ->and($track->supportsWeather('overcast'))->toBeFalse()
        ->and($track->supportsWeather('rain'))->toBeFalse();
});

it('supports DLC tracks with all weather conditions', function () {
    // Test a DLC track that supports all weather conditions
    $track = Track::factory()->create(['key' => 'bleak_city', 'name' => 'Bleak City']);

    // Create all weather conditions
    $clear = WeatherCondition::firstOrCreate(['name' => 'clear']);
    $overcast = WeatherCondition::firstOrCreate(['name' => 'overcast']);
    $rain = WeatherCondition::firstOrCreate(['name' => 'rain']);
    $storm = WeatherCondition::firstOrCreate(['name' => 'storm']);
    $fog = WeatherCondition::firstOrCreate(['name' => 'fog']);

    // Attach all weather conditions
    $track->weatherConditions()->attach([$clear->id, $overcast->id, $rain->id, $storm->id, $fog->id]);

    // Refresh the relationship
    $track->refresh();

    expect($track->weatherConditions)->toHaveCount(5)
        ->and($track->supportsWeather('clear'))->toBeTrue()
        ->and($track->supportsWeather('overcast'))->toBeTrue()
        ->and($track->supportsWeather('rain'))->toBeTrue()
        ->and($track->supportsWeather('storm'))->toBeTrue()
        ->and($track->supportsWeather('fog'))->toBeTrue();
});
