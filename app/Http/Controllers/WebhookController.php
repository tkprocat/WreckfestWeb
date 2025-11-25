<?php

namespace App\Http\Controllers;

use App\Events\ConsoleLogReceived;
use App\Events\EventActivated;
use App\Events\PlayersUpdated;
use App\Events\ServerAttached;
use App\Events\ServerRestartPending;
use App\Events\ServerRestarted;
use App\Events\ServerStarted;
use App\Events\ServerStopped;
use App\Events\TrackChanged;
use App\Services\EventService;
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

    /**
     * Handle event activated webhook from C# server
     */
    public function eventActivated(Request $request, EventService $eventService): JsonResponse
    {
        Log::info('Webhook received: event-activated', $request->all());

        $validated = $request->validate([
            'eventId' => 'required|integer',
            'eventName' => 'nullable|string',
        ]);

        // Mark the event as active in our database
        $eventService->activateEvent($validated['eventId']);

        // Broadcast the event to all connected clients
        broadcast(new EventActivated($validated['eventId'], $validated['eventName'] ?? null));

        Log::info('Event activated: '.$validated['eventId']);

        return response()->json(['success' => true]);
    }

    /**
     * Handle server started webhook from C# server
     */
    public function serverStarted(Request $request): JsonResponse
    {
        Log::info('Webhook received: server-started', $request->all());

        $validated = $request->validate([
            'processId' => 'required|integer',
            'processName' => 'required|string',
            'startTime' => 'required|string',
            'timestamp' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new ServerStarted(
            $validated['processId'],
            $validated['processName'],
            $validated['startTime'],
            $validated['timestamp']
        ));

        Log::info('Server started: PID '.$validated['processId']);

        return response()->json(['success' => true]);
    }

    /**
     * Handle server stopped webhook from C# server
     */
    public function serverStopped(Request $request): JsonResponse
    {
        Log::info('Webhook received: server-stopped', $request->all());

        $validated = $request->validate([
            'processId' => 'required|integer',
            'stopMethod' => 'required|string|in:Graceful,Force',
            'timestamp' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new ServerStopped(
            $validated['processId'],
            $validated['stopMethod'],
            $validated['timestamp']
        ));

        Log::info('Server stopped: PID '.$validated['processId'].' ('.$validated['stopMethod'].')');

        return response()->json(['success' => true]);
    }

    /**
     * Handle server restarted webhook from C# server
     */
    public function serverRestarted(Request $request): JsonResponse
    {
        Log::info('Webhook received: server-restarted', $request->all());

        $validated = $request->validate([
            'oldProcessId' => 'required|integer',
            'newProcessId' => 'required|integer',
            'restartMethod' => 'required|string|in:Command,Full',
            'timestamp' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new ServerRestarted(
            $validated['oldProcessId'],
            $validated['newProcessId'],
            $validated['restartMethod'],
            $validated['timestamp']
        ));

        Log::info('Server restarted: PID '.$validated['oldProcessId'].' → '.$validated['newProcessId'].' ('.$validated['restartMethod'].')');

        return response()->json(['success' => true]);
    }

    /**
     * Handle server attached webhook from C# server
     */
    public function serverAttached(Request $request): JsonResponse
    {
        Log::info('Webhook received: server-attached', $request->all());

        $validated = $request->validate([
            'processId' => 'required|integer',
            'processName' => 'required|string',
            'startTime' => 'required|string',
            'timestamp' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new ServerAttached(
            $validated['processId'],
            $validated['processName'],
            $validated['startTime'],
            $validated['timestamp']
        ));

        Log::info('Server attached: PID '.$validated['processId']);

        return response()->json(['success' => true]);
    }

    /**
     * Handle server restart pending webhook from C# server
     */
    public function serverRestartPending(Request $request): JsonResponse
    {
        Log::info('Webhook received: server-restart-pending', $request->all());

        $validated = $request->validate([
            'minutesRemaining' => 'required|integer',
            'eventName' => 'nullable|string',
            'eventId' => 'nullable|integer',
            'scheduledRestartTime' => 'required|string',
            'timestamp' => 'required|string',
        ]);

        // Broadcast the event to all connected clients
        broadcast(new ServerRestartPending(
            $validated['minutesRemaining'],
            $validated['eventName'] ?? null,
            $validated['eventId'] ?? null,
            $validated['scheduledRestartTime'],
            $validated['timestamp']
        ));

        Log::info('Server restart pending: '.$validated['minutesRemaining'].' minutes remaining');

        return response()->json(['success' => true]);
    }

    /**
     * Handle console logs webhook from C# server
     */
    public function consoleLogs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logs' => 'required|array',
            'logs.*' => 'required|string',
        ]);

        // Broadcast the logs to all connected clients via Reverb
        broadcast(new ConsoleLogReceived($validated['logs']));

        return response()->json(['success' => true]);
    }
}
