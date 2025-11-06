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
        'recurring_pattern',
        'created_by',
    ];

    protected $casts = [
        'server_config' => 'array',
        'recurring_pattern' => 'array',
        'start_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function trackCollection(): BelongsTo
    {
        return $this->belongsTo(TrackCollection::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
