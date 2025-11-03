<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class DuplicateTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Duplicate an existing track collection with all its tracks and metadata. Creates a copy with a new name.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $collectionId = $request->get('id');
        $collectionName = $request->get('name');
        $newName = $request->get('new_name');

        // Validate required parameters
        if (!$collectionId && !$collectionName) {
            return Response::text("Error: Either 'id' or 'name' parameter is required.");
        }

        if (!$newName) {
            return Response::text("Error: 'new_name' parameter is required.");
        }

        // Check if new name already exists
        $existingWithNewName = TrackCollection::where('name', $newName)->first();
        if ($existingWithNewName) {
            return Response::text("Error: A collection with the name '{$newName}' already exists. Please choose a different name.");
        }

        // Find the collection to duplicate
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

        // Create the duplicate
        $duplicate = new TrackCollection();
        $duplicate->name = $newName;
        $duplicate->tracks = $collection->tracks; // This will be the same array
        $duplicate->save();

        // Store the new collection ID for auto-switching
        cache()->put('last_created_collection_id', $duplicate->id, now()->addMinutes(5));

        return Response::text("✓ Successfully duplicated collection '{$collection->name}' to '{$newName}'\n\nNew Collection ID: {$duplicate->id}\nTracks copied: " . count($duplicate->tracks) . "\n\nThe UI will automatically switch to display the new collection.");
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
                ->description('The collection ID to duplicate. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name to search for and duplicate. Either id or name must be provided.'),
            'new_name' => $schema->string()
                ->description('The name for the new duplicated collection. Must be unique.'),
        ];
    }
}
