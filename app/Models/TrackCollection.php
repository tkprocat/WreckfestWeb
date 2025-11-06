<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackCollection extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'tracks',
    ];

    // Note: tracks attribute is handled by the Attribute accessor below, not casts

    /**
     * Normalize tracks to ensure they have the proper object structure.
     * Converts old string-based tracks to proper track objects.
     */
    protected function tracks(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $tracks = json_decode($value, true) ?? [];

                return array_map(function ($track) {
                    if (is_string($track)) {
                        // Convert simple string to proper track object
                        return [
                            'track' => $track,
                            'gamemode' => null,
                            'laps' => null,
                            'bots' => null,
                            'numTeams' => null,
                            'carResetDisabled' => false,
                            'wrongWayLimiterDisabled' => false,
                            'carClassRestriction' => null,
                            'carRestriction' => null,
                            'weather' => null,
                        ];
                    }

                    // Already an object, ensure it has required fields
                    return array_merge([
                        'gamemode' => null,
                        'laps' => null,
                        'bots' => null,
                        'numTeams' => null,
                        'carResetDisabled' => false,
                        'wrongWayLimiterDisabled' => false,
                        'carClassRestriction' => null,
                        'carRestriction' => null,
                        'weather' => null,
                    ], $track);
                }, $tracks);
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }
}
