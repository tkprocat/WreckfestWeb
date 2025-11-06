<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WeatherCondition extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the tracks that support this weather condition
     */
    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class);
    }
}
