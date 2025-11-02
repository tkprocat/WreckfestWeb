<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Update a track collection by replacing its entire tracks array with a new set of tracks. Use this when you want to completely replace the tracks in a collection. Each track should be a track ID (e.g., "track/wild_valley/valley_edge_short").';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $tracks = $request->get('tracks', []);

        // Find collection by ID or name
        if ($id) {
            $collection = TrackCollection::find($id);
            if (! $collection) {
                return Response::error("Collection with ID {$id} not found");
            }
        } elseif ($name) {
            $collection = TrackCollection::where('name', $name)->first();
            if (! $collection) {
                return Response::error("Collection with name '{$name}' not found");
            }
        } else {
            return Response::error('Either id or name must be provided');
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

        // Update the collection
        $collection->update(['tracks' => $normalizedTracks]);

        $output = "✅ Successfully updated collection: {$collection->name}\n";
        $output .= "Updated at: {$collection->updated_at->format('Y-m-d H:i:s')}\n";
        $output .= 'Total tracks: '.count($normalizedTracks)."\n\n";

        if (! empty($normalizedTracks)) {
            $output .= "Track list:\n";
            foreach ($normalizedTracks as $index => $track) {
                $trackId = is_array($track) ? ($track['track'] ?? 'unknown') : $track;
                $output .= '  '.($index + 1).". {$trackId}\n";
            }
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
            'id' => $schema->integer()
                ->description('The collection ID to update. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name to search for. Either id or name must be provided.'),
            'tracks' => $schema->array()
                ->items($schema->string())
                ->description('The new array of tracks. Provide an array of track IDs (e.g., ["track/wild_valley/valley_edge_short", "track/hilltop_stadium/oval"]).'),
        ];
    }
}
