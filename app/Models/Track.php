<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
    ];

    /**
     * Get the variants for this track location
     */
    public function variants(): HasMany
    {
        return $this->hasMany(TrackVariant::class);
    }

    /**
     * Get the weather conditions supported by this track
     */
    public function weatherConditions(): BelongsToMany
    {
        return $this->belongsToMany(WeatherCondition::class);
    }

    /**
     * Check if this track supports a specific weather condition
     */
    public function supportsWeather(string $weatherName): bool
    {
        return $this->weatherConditions()->where('name', $weatherName)->exists();
    }
}
