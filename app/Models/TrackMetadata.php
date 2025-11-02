<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
