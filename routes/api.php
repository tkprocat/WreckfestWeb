<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Set user timezone in session (auto-detected from browser)
Route::post('/set-timezone', function (Request $request) {
    $timezone = $request->input('timezone', 'UTC');

    // Validate timezone
    if (in_array($timezone, timezone_identifiers_list())) {
        session(['user_timezone' => $timezone]);
        return response()->json(['success' => true, 'timezone' => $timezone]);
    }

    return response()->json(['success' => false, 'error' => 'Invalid timezone'], 400);
})->middleware('web');

// Webhooks from C# WreckfestController
Route::post('/webhooks/players-updated', [WebhookController::class, 'playersUpdated']);
Route::post('/webhooks/track-changed', [WebhookController::class, 'trackChanged']);
Route::post('/webhooks/event-activated', [WebhookController::class, 'eventActivated']);
Route::post('/webhooks/server-started', [WebhookController::class, 'serverStarted']);
Route::post('/webhooks/server-stopped', [WebhookController::class, 'serverStopped']);
Route::post('/webhooks/server-restarted', [WebhookController::class, 'serverRestarted']);
Route::post('/webhooks/server-attached', [WebhookController::class, 'serverAttached']);
Route::post('/webhooks/server-restart-pending', [WebhookController::class, 'serverRestartPending']);


Route::get('test', function () {
    $variants = \App\Models\TrackVariant::with('track')->get();
    $allTracks = [];

    foreach ($variants as $variant) {
        $allTracks[$variant->variant_id] = $variant->full_name;
    }

    return $allTracks;
});
