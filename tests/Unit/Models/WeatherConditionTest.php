<?php

use App\Models\Track;
use App\Models\WeatherCondition;

it('has fillable attributes', function () {
    $weather = new WeatherCondition([
        'name' => 'clear',
    ]);

    expect($weather->name)->toBe('clear');
});

it('belongs to many tracks', function () {
    $weather = WeatherCondition::create(['name' => 'clear']);
    $track1 = Track::factory()->create();
    $track2 = Track::factory()->create();

    $weather->tracks()->attach([$track1->id, $track2->id]);

    expect($weather->tracks)->toHaveCount(2)
        ->and($weather->tracks->pluck('id')->toArray())->toContain($track1->id)
        ->and($weather->tracks->pluck('id')->toArray())->toContain($track2->id);
});

it('can be associated with multiple tracks', function () {
    $clear = WeatherCondition::create(['name' => 'clear']);
    $overcast = WeatherCondition::create(['name' => 'overcast']);

    $track1 = Track::factory()->create();
    $track2 = Track::factory()->create();
    $track3 = Track::factory()->create();

    // Track 1 supports both clear and overcast
    $track1->weatherConditions()->attach([$clear->id, $overcast->id]);

    // Track 2 supports only clear
    $track2->weatherConditions()->attach($clear->id);

    // Track 3 supports only overcast
    $track3->weatherConditions()->attach($overcast->id);

    // Refresh to load relationships
    $clear->refresh();
    $overcast->refresh();

    expect($clear->tracks)->toHaveCount(2)
        ->and($overcast->tracks)->toHaveCount(2);
});

it('has unique name', function () {
    WeatherCondition::create(['name' => 'unique_weather']);

    expect(function () {
        WeatherCondition::create(['name' => 'unique_weather']);
    })->toThrow(\Illuminate\Database\QueryException::class);
});
