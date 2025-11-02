<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrackVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'variant_id',
        'name',
        'game_mode',
        'weather_conditions',
    ];

    protected $casts = [
        'weather_conditions' => 'array',
    ];

    /**
     * Get the track location this variant belongs to
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Get the tags associated with this track variant
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'track_variant_tag');
    }

    /**
     * Get the full display name (Location - Variant)
     */
    public function getFullNameAttribute(): string
    {
        return $this->track->name.' - '.$this->name;
    }
}
