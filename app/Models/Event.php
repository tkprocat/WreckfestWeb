<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'track_collection_id',
        'server_config',
        'start_time',
        'is_active',
        'repeat',
        'created_by',
    ];

    protected $casts = [
        'server_config' => 'array',
        'repeat' => 'array',
        'start_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if this event is recurring
     */
    public function isRecurring(): bool
    {
        return !empty($this->repeat['frequency']);
    }

    public function trackCollection(): BelongsTo
    {
        return $this->belongsTo(TrackCollection::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
