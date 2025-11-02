<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class RemoveTracksFromCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Remove tracks from a collection by track ID or by index position. You can specify track IDs to remove all matching tracks, or indices to remove tracks at specific positions (0-based).';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $trackIds = $request->get('track_ids', []);
        $indices = $request->get('indices', []);

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

        $currentTracks = $collection->tracks ?? [];

        if (empty($currentTracks)) {
            return Response::error('Collection has no tracks to remove');
        }

        $removedCount = 0;
        $removedTracks = [];

        // Remove by track IDs
        if (! empty($trackIds) && is_array($trackIds)) {
            foreach ($currentTracks as $index => $track) {
                $trackId = is_array($track) ? ($track['track'] ?? null) : $track;

                if (in_array($trackId, $trackIds)) {
                    $removedTracks[] = $trackId;
                    unset($currentTracks[$index]);
                    $removedCount++;
                }
            }
        }

        // Remove by indices
        if (! empty($indices) && is_array($indices)) {
            // Sort indices in descending order to remove from end first
            rsort($indices);

            foreach ($indices as $index) {
                if (isset($currentTracks[$index])) {
                    $track = $currentTracks[$index];
                    $trackId = is_array($track) ? ($track['track'] ?? 'unknown') : $track;
                    $removedTracks[] = $trackId;
                    unset($currentTracks[$index]);
                    $removedCount++;
                }
            }
        }

        // Re-index array
        $currentTracks = array_values($currentTracks);

        // Update the collection
        $collection->update(['tracks' => $currentTracks]);

        if ($removedCount === 0) {
            return Response::text("⚠️ No tracks were removed from: {$collection->name}\n(No matching tracks or indices found)");
        }

        $output = "✅ Successfully removed {$removedCount} track(s) from: {$collection->name}\n";
        $output .= "Tracks remaining: ".count($currentTracks)."\n\n";

        if (! empty($removedTracks)) {
            $output .= "Removed tracks:\n";
            foreach ($removedTracks as $index => $trackId) {
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
                ->description('The collection ID. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name. Either id or name must be provided.'),
            'track_ids' => $schema->array()
                ->items($schema->string())
                ->description('Array of track IDs to remove. All matching tracks will be removed.'),
            'indices' => $schema->array()
                ->items($schema->integer())
                ->description('Array of indices (0-based positions) to remove specific track positions.'),
        ];
    }
}
