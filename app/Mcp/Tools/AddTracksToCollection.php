<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AddTracksToCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Add one or more tracks to an existing track collection. The tracks will be appended to the end of the collection. Each track should be a track ID (e.g., "track/wild_valley/valley_edge_short") or a track object with track, gamemode, laps, etc.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $tracksToAdd = $request->get('tracks', []);

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

        // Validate tracks array
        if (! is_array($tracksToAdd) || empty($tracksToAdd)) {
            return Response::error('Tracks must be a non-empty array');
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
        }, $tracksToAdd);

        // Get current tracks and append new ones
        $currentTracks = $collection->tracks ?? [];
        $updatedTracks = array_merge($currentTracks, $normalizedTracks);

        // Update the collection
        $collection->update(['tracks' => $updatedTracks]);

        $output = "✅ Successfully added ".count($normalizedTracks)." track(s) to: {$collection->name}\n";
        $output .= "Total tracks now: ".count($updatedTracks)."\n\n";

        $output .= "Added tracks:\n";
        foreach ($normalizedTracks as $index => $track) {
            $trackId = is_array($track) ? ($track['track'] ?? 'unknown') : $track;
            $output .= '  '.($index + 1).". {$trackId}\n";
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
                ->description('The collection ID. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name. Either id or name must be provided.'),
            'tracks' => $schema->array()
                ->items($schema->string())
                ->description('Array of track IDs to add (e.g., ["track/wild_valley/valley_edge_short", "track/hilltop_stadium/oval"]).'),
        ];
    }
}
