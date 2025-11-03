<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class RenameTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Rename an existing track collection without creating a duplicate.";

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

        // Find the collection to rename
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

        $oldName = $collection->name;

        // Rename the collection
        $collection->name = $newName;
        $collection->save();

        return Response::text("✓ Successfully renamed collection from '{$oldName}' to '{$newName}'\n\nCollection ID: {$collection->id}\nTracks: " . count($collection->tracks));
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
            'new_name' => $schema->string()
                ->description('The new name for the collection. Must be unique.'),
        ];
    }
}
