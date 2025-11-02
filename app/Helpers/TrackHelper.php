<?php

namespace App\Helpers;

use App\Models\TrackVariant;

class TrackHelper
{
    /**
     * Convert a track ID to human-readable track details
     *
     * @param  string  $trackId  The track variant ID (e.g., "speedway2_inner_oval")
     * @return array Array containing track details
     */
    public static function getTrackDetails(string $trackId): array
    {
        $variant = TrackVariant::with('track')->where('variant_id', $trackId)->first();

        if (! $variant) {
            return [
                'fullName' => $trackId,
                'location' => 'Unknown',
                'variant' => $trackId,
                'trackId' => $trackId,
                'isDerby' => false,
                'weather' => [],
            ];
        }

        return [
            'fullName' => $variant->full_name,
            'location' => $variant->track->name,
            'variant' => $variant->name,
            'trackId' => $variant->variant_id,
            'isDerby' => $variant->game_mode === 'Derby',
            'weather' => $variant->weather_conditions ?? array_keys(config('wreckfest.weather_conditions', [])),
        ];
    }

    /**
     * Get just the human-readable name for a track ID
     */
    public static function getTrackName(string $trackId): string
    {
        return self::getTrackDetails($trackId)['fullName'];
    }

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
        $derbyGamemodes = config('wreckfest.derby_gamemodes', []);
        $isDerbyGamemode = in_array($gamemode, $derbyGamemodes);

        $variants = TrackVariant::with('track')
            ->where('game_mode', $isDerbyGamemode ? 'Derby' : 'Racing')
            ->get();

        $compatibleTracks = [];
        foreach ($variants as $variant) {
            $compatibleTracks[$variant->variant_id] = $variant->full_name;
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
        $variant = TrackVariant::where('variant_id', $variantId)->first();

        return $variant ? $variant->game_mode === 'Derby' : false;
    }

    /**
     * Get supported weather conditions for a track variant
     * Returns all weather types if track has no restrictions
     */
    public static function getSupportedWeatherForTrack(string $variantId): array
    {
        $variant = TrackVariant::where('variant_id', $variantId)->first();

        if (! $variant) {
            return array_keys(config('wreckfest.weather_conditions', []));
        }

        // If weather_conditions is null, track supports all weather types
        if ($variant->weather_conditions === null) {
            return array_keys(config('wreckfest.weather_conditions', []));
        }

        return $variant->weather_conditions;
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

    /**
     * Get all tags for a track variant
     *
     * @param  string  $variantId  The track variant ID
     * @return array Array of tag names
     */
    public static function getTagsForTrack(string $variantId): array
    {
        $variant = TrackVariant::with('tags')->where('variant_id', $variantId)->first();

        if (! $variant) {
            return [];
        }

        return $variant->tags->pluck('name')->toArray();
    }

    /**
     * Get all tracks that have a specific tag
     *
     * @param  string  $tagSlug  The tag slug
     * @return array Array of track variant IDs and names
     */
    public static function getTracksWithTag(string $tagSlug): array
    {
        $tag = \App\Models\Tag::where('slug', $tagSlug)->first();

        if (! $tag) {
            return [];
        }

        $variants = $tag->trackVariants()->with('track')->get();

        $tracks = [];
        foreach ($variants as $variant) {
            $tracks[$variant->variant_id] = $variant->full_name;
        }

        return $tracks;
    }

    /**
     * Get tracks that have all or any of the specified tags
     *
     * @param  array  $tagSlugs  Array of tag slugs
     * @param  bool  $matchAll  If true, tracks must have ALL tags. If false, tracks must have ANY tag.
     * @return array Array of track variant IDs and names
     */
    public static function getTracksWithTags(array $tagSlugs, bool $matchAll = false): array
    {
        $query = TrackVariant::with(['track', 'tags']);

        if ($matchAll) {
            // Track must have ALL specified tags
            foreach ($tagSlugs as $slug) {
                $query->whereHas('tags', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            }
        } else {
            // Track must have ANY of the specified tags
            $query->whereHas('tags', function ($q) use ($tagSlugs) {
                $q->whereIn('slug', $tagSlugs);
            });
        }

        $variants = $query->get();

        $tracks = [];
        foreach ($variants as $variant) {
            $tracks[$variant->variant_id] = $variant->full_name;
        }

        return $tracks;
    }

    /**
     * Check if a track has a specific tag
     *
     * @param  string  $variantId  The track variant ID
     * @param  string  $tagSlug  The tag slug
     * @return bool
     */
    public static function trackHasTag(string $variantId, string $tagSlug): bool
    {
        $variant = TrackVariant::with('tags')->where('variant_id', $variantId)->first();

        if (! $variant) {
            return false;
        }

        return $variant->tags->contains('slug', $tagSlug);
    }
}
