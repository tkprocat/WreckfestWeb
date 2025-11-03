<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ShuffleTracksInCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Randomize the order of tracks in a collection. Useful for creating variety in track rotation.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $collectionId = $request->get('id');
        $collectionName = $request->get('name');

        // Validate required parameters
        if (!$collectionId && !$collectionName) {
            return Response::text("Error: Either 'id' or 'name' parameter is required.");
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

        if ($trackCount < 2) {
            return Response::text("Collection '{$collection->name}' has only {$trackCount} track(s). Need at least 2 tracks to shuffle.");
        }

        // Shuffle the tracks
        shuffle($tracks);

        // Save the shuffled tracks
        $collection->tracks = $tracks;
        $collection->save();

        // Build output showing first few tracks
        $output = "✓ Successfully shuffled {$trackCount} tracks in '{$collection->name}'\n\n";
        $output .= "New track order (first 5):\n";

        $displayCount = min(5, $trackCount);
        for ($i = 0; $i < $displayCount; $i++) {
            $trackName = $tracks[$i]['track'] ?? 'Unknown';
            $output .= "  " . ($i + 1) . ". {$trackName}\n";
        }

        if ($trackCount > 5) {
            $output .= "  ... and " . ($trackCount - 5) . " more\n";
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
                ->description('The collection name to search for. Either id or name must be provided.'),
        ];
    }
}
