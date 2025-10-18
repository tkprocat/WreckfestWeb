<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackCollection extends Model
{
    protected $fillable = [
        'name',
        'tracks',
    ];

    protected $casts = [
        'tracks' => 'array',
    ];
}
