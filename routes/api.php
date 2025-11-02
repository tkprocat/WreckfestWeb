<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Webhooks from C# WreckfestController
Route::post('/webhooks/players-updated', [WebhookController::class, 'playersUpdated']);
Route::post('/webhooks/track-changed', [WebhookController::class, 'trackChanged']);


Route::get('test', function () {
    $variants = \App\Models\TrackVariant::with('track')->get();
    $allTracks = [];

    foreach ($variants as $variant) {
        $allTracks[$variant->variant_id] = $variant->full_name;
    }

    return $allTracks;
});
