<?php

use App\Helpers\TrackHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\TrackSeeder::class);
});

it('identifies derby gamemodes correctly', function () {
    expect(TrackHelper::isDerbyGamemode('derby'))->toBeTrue();
    expect(TrackHelper::isDerbyGamemode('derby deathmatch'))->toBeTrue();
    expect(TrackHelper::isDerbyGamemode('team derby'))->toBeTrue();
    expect(TrackHelper::isDerbyGamemode('racing'))->toBeFalse();
    expect(TrackHelper::isDerbyGamemode('team race'))->toBeFalse();
});

it('identifies racing gamemodes correctly', function () {
    expect(TrackHelper::isRacingGamemode('racing'))->toBeTrue();
    expect(TrackHelper::isRacingGamemode('team race'))->toBeTrue();
    expect(TrackHelper::isRacingGamemode('elimination race'))->toBeTrue();
    expect(TrackHelper::isRacingGamemode('derby'))->toBeFalse();
    expect(TrackHelper::isRacingGamemode('derby deathmatch'))->toBeFalse();
});

it('identifies derby-only tracks correctly', function () {
    expect(TrackHelper::isDerbyOnlyTrack('bigstadium_demolition_arena'))->toBeTrue();
    expect(TrackHelper::isDerbyOnlyTrack('urban07'))->toBeTrue(); // Thunderbowl - Demolition Arena
    expect(TrackHelper::isDerbyOnlyTrack('loop'))->toBeFalse(); // Deathloop - racing track
    expect(TrackHelper::isDerbyOnlyTrack('tarmac1_main_circuit'))->toBeFalse();
});

it('correctly determines track-gamemode compatibility', function () {
    // Derby tracks should only work with derby gamemodes
    expect(TrackHelper::isTrackCompatibleWithGamemode('bigstadium_demolition_arena', 'derby'))->toBeTrue();
    expect(TrackHelper::isTrackCompatibleWithGamemode('bigstadium_demolition_arena', 'derby deathmatch'))->toBeTrue();
    expect(TrackHelper::isTrackCompatibleWithGamemode('bigstadium_demolition_arena', 'racing'))->toBeFalse();
    expect(TrackHelper::isTrackCompatibleWithGamemode('bigstadium_demolition_arena', 'team race'))->toBeFalse();

    // Racing tracks should only work with racing gamemodes
    expect(TrackHelper::isTrackCompatibleWithGamemode('loop', 'racing'))->toBeTrue();
    expect(TrackHelper::isTrackCompatibleWithGamemode('loop', 'team race'))->toBeTrue();
    expect(TrackHelper::isTrackCompatibleWithGamemode('loop', 'elimination race'))->toBeTrue();
    expect(TrackHelper::isTrackCompatibleWithGamemode('loop', 'derby'))->toBeFalse();
    expect(TrackHelper::isTrackCompatibleWithGamemode('loop', 'derby deathmatch'))->toBeFalse();
});

it('returns only compatible tracks for derby gamemode', function () {
    $derbyTracks = TrackHelper::getTracksForGamemode('derby');

    expect($derbyTracks)->toHaveKey('bigstadium_demolition_arena');
    expect($derbyTracks)->toHaveKey('urban07');
    expect($derbyTracks)->not->toHaveKey('loop');
    expect($derbyTracks)->not->toHaveKey('tarmac1_main_circuit');
});

it('returns only compatible tracks for racing gamemode', function () {
    $racingTracks = TrackHelper::getTracksForGamemode('racing');

    expect($racingTracks)->toHaveKey('loop');
    expect($racingTracks)->toHaveKey('tarmac1_main_circuit');
    expect($racingTracks)->not->toHaveKey('bigstadium_demolition_arena');
    expect($racingTracks)->not->toHaveKey('urban07');
});

it('gets supported weather for tracks with restrictions', function () {
    // Sandstone Raceway - clear only
    $sandstoneWeather = TrackHelper::getSupportedWeatherForTrack('sandpit1_long_loop');
    expect($sandstoneWeather)->toBe(['clear']);

    // Glendale Countryside - overcast only
    $glendaleWeather = TrackHelper::getSupportedWeatherForTrack('field_derby_arena');
    expect($glendaleWeather)->toBe(['overcast']);

    // Fairfield venues - clear, overcast, fog
    $fairfieldWeather = TrackHelper::getSupportedWeatherForTrack('smallstadium_demolition_arena');
    expect($fairfieldWeather)->toBe(['clear', 'overcast', 'fog']);
});

it('returns all weather types for tracks without restrictions', function () {
    // Tracks not in the compatibility map should support all weather
    $allWeather = TrackHelper::getSupportedWeatherForTrack('some_unrestricted_track');
    expect($allWeather)->toContain('clear');
    expect($allWeather)->toContain('overcast');
    expect($allWeather)->toContain('rain');
    expect($allWeather)->toContain('storm');
    expect($allWeather)->toContain('fog');
});

it('checks if weather is supported for a track', function () {
    // Sandstone Raceway only supports clear
    expect(TrackHelper::isWeatherSupportedForTrack('sandpit1_long_loop', 'clear'))->toBeTrue();
    expect(TrackHelper::isWeatherSupportedForTrack('sandpit1_long_loop', 'rain'))->toBeFalse();
    expect(TrackHelper::isWeatherSupportedForTrack('sandpit1_long_loop', 'storm'))->toBeFalse();
    expect(TrackHelper::isWeatherSupportedForTrack('sandpit1_long_loop', 'fog'))->toBeFalse();

    // Random is always supported
    expect(TrackHelper::isWeatherSupportedForTrack('sandpit1_long_loop', 'random'))->toBeTrue();
    expect(TrackHelper::isWeatherSupportedForTrack('field_derby_arena', 'random'))->toBeTrue();
});

it('gets weather options for a track including random', function () {
    // Glendale Countryside - overcast only
    $glendaleOptions = TrackHelper::getWeatherOptionsForTrack('field_derby_arena');
    expect($glendaleOptions)->toHaveKey('random');
    expect($glendaleOptions)->toHaveKey('overcast');
    expect($glendaleOptions)->not->toHaveKey('clear');
    expect($glendaleOptions)->not->toHaveKey('rain');

    // Sandstone Raceway - clear only
    $sandstoneOptions = TrackHelper::getWeatherOptionsForTrack('sandpit1_long_loop');
    expect($sandstoneOptions)->toHaveKey('random');
    expect($sandstoneOptions)->toHaveKey('clear');
    expect($sandstoneOptions)->not->toHaveKey('overcast');
});
