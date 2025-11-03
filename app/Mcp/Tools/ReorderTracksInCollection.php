<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ReorderTracksInCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Reorder tracks in a collection by moving a track from one position to another. Positions are 0-indexed.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $collectionId = $request->get('id');
        $collectionName = $request->get('name');
        $fromIndex = $request->get('from_index');
        $toIndex = $request->get('to_index');

        // Validate required parameters
        if (!$collectionId && !$collectionName) {
            return Response::text("Error: Either 'id' or 'name' parameter is required.");
        }

        if ($fromIndex === null || $toIndex === null) {
            return Response::text("Error: Both 'from_index' and 'to_index' parameters are required.");
        }

        if (!is_numeric($fromIndex) || !is_numeric($toIndex)) {
            return Response::text("Error: 'from_index' and 'to_index' must be numeric.");
        }

        // Find the collection
        $query = TrackCollection::query();
        if ($collectionId) {
            $query->where('id', $collectionId);
        } else {
            $query->where('name', 'LIKE', "%{$collectionName}%");
        }

        $collection = $query->first();

        if (!$collection) {
            $identifier = $collectionId ?? $collectionName;
            return Response::text("Error: Collection not found with identifier: {$identifier}");
        }

        $tracks = $collection->tracks;
        $trackCount = count($tracks);

        // Validate indices
        if ($fromIndex < 0 || $fromIndex >= $trackCount) {
            return Response::text("Error: 'from_index' ({$fromIndex}) is out of range. Collection has {$trackCount} tracks (indices 0-" . ($trackCount - 1) . ").");
        }

        if ($toIndex < 0 || $toIndex >= $trackCount) {
            return Response::text("Error: 'to_index' ({$toIndex}) is out of range. Collection has {$trackCount} tracks (indices 0-" . ($trackCount - 1) . ").");
        }

        // Perform the reorder
        $track = $tracks[$fromIndex];
        array_splice($tracks, $fromIndex, 1);  // Remove from old position
        array_splice($tracks, $toIndex, 0, [$track]);  // Insert at new position

        // Save the reordered tracks
        $collection->tracks = $tracks;
        $collection->save();

        $trackName = $track['track'] ?? 'Unknown';

        return Response::text("✓ Successfully reordered tracks in '{$collection->name}'\n\nMoved: {$trackName}\nFrom position: {$fromIndex}\nTo position: {$toIndex}\n\nTotal tracks: {$trackCount}");
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
                ->description('The collection name to search for. Either id or name must be provided.'),
            'from_index' => $schema->integer()
                ->description('The current position of the track to move (0-indexed).'),
            'to_index' => $schema->integer()
                ->description('The target position to move the track to (0-indexed).'),
        ];
    }
}
