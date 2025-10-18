<?php

namespace App\Helpers;

class TrackHelper
{
    /**
     * Check if a track is compatible with a given gamemode
     */
    public static function isTrackCompatibleWithGamemode(string $trackId, string $gamemode): bool
    {
        $derbyGamemodes = config('wreckfest.derby_gamemodes', []);
        $isDerbyGamemode = in_array($gamemode, $derbyGamemodes);
        $isDerbyTrack = self::isDerbyOnlyTrack($trackId);

        // Derby tracks can only be used with derby gamemodes
        if ($isDerbyTrack) {
            return $isDerbyGamemode;
        }

        // Racing tracks can only be used with racing gamemodes
        return ! $isDerbyGamemode;
    }

    /**
     * Get all tracks compatible with a given gamemode
     */
    public static function getTracksForGamemode(string $gamemode): array
    {
        $trackLocations = config('wreckfest.tracks', []);
        $compatibleTracks = [];

        foreach ($trackLocations as $locationKey => $location) {
            $locationName = $location['name'] ?? $locationKey;
            $variants = $location['variants'] ?? [];

            foreach ($variants as $variantId => $variant) {
                if (self::isTrackCompatibleWithGamemode($variantId, $gamemode)) {
                    $variantName = is_array($variant) ? ($variant['name'] ?? $variantId) : $variant;
                    $compatibleTracks[$variantId] = $locationName . ' - ' . $variantName;
                }
            }
        }

        return $compatibleTracks;
    }

    /**
     * Check if a gamemode is a derby gamemode
     */
    public static function isDerbyGamemode(string $gamemode): bool
    {
        return in_array($gamemode, config('wreckfest.derby_gamemodes', []));
    }

    /**
     * Check if a gamemode is a racing gamemode
     */
    public static function isRacingGamemode(string $gamemode): bool
    {
        return in_array($gamemode, config('wreckfest.racing_gamemodes', []));
    }

    /**
     * Check if a track variant is a derby-only track
     */
    public static function isDerbyOnlyTrack(string $variantId): bool
    {
        $trackLocations = config('wreckfest.tracks', []);

        foreach ($trackLocations as $location) {
            $variants = $location['variants'] ?? [];

            foreach ($variants as $id => $variant) {
                if ($id === $variantId) {
                    // If variant is an array, check the derby flag
                    if (is_array($variant)) {
                        return $variant['derby'] ?? false;
                    }
                    // If variant is a string, it's not derby-only
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Get supported weather conditions for a track variant
     * Returns all weather types if track has no restrictions
     */
    public static function getSupportedWeatherForTrack(string $variantId): array
    {
        $trackLocations = config('wreckfest.tracks', []);

        // Find the track location for this variant
        foreach ($trackLocations as $location) {
            $variants = $location['variants'] ?? [];

            if (isset($variants[$variantId])) {
                $weather = $location['weather'] ?? null;

                // If weather is null, track supports all weather types
                if ($weather === null) {
                    return array_keys(config('wreckfest.weather_conditions', []));
                }

                // Return the specific weather conditions
                return $weather;
            }
        }

        // If variant not found, return all weather types
        return array_keys(config('wreckfest.weather_conditions', []));
    }

    /**
     * Check if a weather condition is supported for a track
     */
    public static function isWeatherSupportedForTrack(string $trackId, string $weather): bool
    {
        // Random is always supported
        if ($weather === 'random') {
            return true;
        }

        $supportedWeather = self::getSupportedWeatherForTrack($trackId);
        return in_array($weather, $supportedWeather);
    }

    /**
     * Get all weather options for a track (including random)
     */
    public static function getWeatherOptionsForTrack(string $trackId): array
    {
        $weatherConditions = config('wreckfest.weather_conditions', []);
        $supportedWeather = self::getSupportedWeatherForTrack($trackId);

        $options = [];

        // Always include random
        $options['random'] = $weatherConditions['random'] ?? 'Random';

        // Add supported weather types
        foreach ($supportedWeather as $weather) {
            if (isset($weatherConditions[$weather])) {
                $options[$weather] = $weatherConditions[$weather];
            }
        }

        return $options;
    }
}
