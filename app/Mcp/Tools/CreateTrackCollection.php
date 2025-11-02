<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Create a new track collection with an optional initial set of tracks. Provide a unique name for the collection and optionally an array of track IDs to populate it.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $name = $request->get('name');
        $tracks = $request->get('tracks', []);

        // Validate name is provided
        if (! $name) {
            return Response::error('Collection name is required');
        }

        // Check if collection with this name already exists
        if (TrackCollection::where('name', $name)->exists()) {
            return Response::error("A collection with name '{$name}' already exists. Please choose a different name or use UpdateTrackCollection to modify the existing one.");
        }

        // Validate tracks array structure
        if (! is_array($tracks)) {
            return Response::error('Tracks must be an array of track IDs or track objects');
        }

        // Normalize tracks to proper object format
        $normalizedTracks = array_map(function ($track) {
            if (is_string($track)) {
                // Convert simple string to proper track object
                return [
                    'track' => $track,
                    'gamemode' => null,
                    'laps' => null,
                    'bots' => null,
                    'numTeams' => null,
                    'carResetDisabled' => false,
                    'wrongWayLimiterDisabled' => false,
                    'carClassRestriction' => null,
                    'carRestriction' => null,
                    'weather' => null,
                ];
            }

            // Already an object, ensure it has required fields
            return array_merge([
                'gamemode' => null,
                'laps' => null,
                'bots' => null,
                'numTeams' => null,
                'carResetDisabled' => false,
                'wrongWayLimiterDisabled' => false,
                'carClassRestriction' => null,
                'carRestriction' => null,
                'weather' => null,
            ], $track);
        }, $tracks);

        // Create the collection
        $collection = TrackCollection::create([
            'name' => $name,
            'tracks' => $normalizedTracks,
        ]);

        // Store the collection ID in cache for auto-switching (MCP runs in different session context)
        // Use a short TTL since this should be picked up immediately
        cache()->put('last_created_collection_id', $collection->id, now()->addMinutes(5));

        logger()->info('MCP: Created collection', [
            'id' => $collection->id,
            'name' => $collection->name,
            'cached' => cache('last_created_collection_id')
        ]);

        $output = "✅ Successfully created collection: {$collection->name}\n";
        $output .= "ID: {$collection->id}\n";
        $output .= "Created at: {$collection->created_at->format('Y-m-d H:i:s')}\n";
        $output .= 'Total tracks: '.count($normalizedTracks)."\n\n";

        if (! empty($normalizedTracks)) {
            $output .= "Initial track list:\n";
            foreach ($normalizedTracks as $index => $track) {
                $trackId = is_array($track) ? ($track['track'] ?? 'unknown') : $track;
                $output .= '  '.($index + 1).". {$trackId}\n";
            }
        } else {
            $output .= "The collection was created empty. You can add tracks using AddTracksToCollection.\n";
        }

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('The name for the new collection. Must be unique.')
                ->required(),
            'tracks' => $schema->array()
                ->items($schema->string())
                ->description('Optional: An array of track IDs to initialize the collection with (e.g., ["track/wild_valley/valley_edge_short", "track/hilltop_stadium/oval"]). If not provided, an empty collection will be created.'),
        ];
    }
}
