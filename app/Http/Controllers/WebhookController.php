<?php

namespace App\Http\Controllers;

use App\Events\PlayersUpdated;
use App\Events\TrackChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle players updated webhook from C# server
     */
    public function playersUpdated(Request $request): JsonResponse
    {
        Log::info('Webhook received: players-updated', $request->all());

        $validated = $request->validate([
            'players' => 'required|array',
            'players.*.name' => 'required|string',
            'players.*.isBot' => 'required|boolean',
            'players.*.playerId' => 'nullable|integer',
            'players.*.score' => 'nullable|integer',
            'players.*.vehicle' => 'nullable|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new PlayersUpdated($validated['players']));

        Log::info('Broadcast sent for players updated: '.count($validated['players']).' players');

        return response()->json(['success' => true]);
    }

    /**
     * Handle track changed webhook from C# server
     */
    public function trackChanged(Request $request): JsonResponse
    {
        Log::info('Webhook received: track-changed', $request->all());

        $validated = $request->validate([
            'trackId' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new TrackChanged($validated['trackId']));

        Log::info('Broadcast sent for track changed: '.$validated['trackId']);

        return response()->json(['success' => true]);
    }
}
