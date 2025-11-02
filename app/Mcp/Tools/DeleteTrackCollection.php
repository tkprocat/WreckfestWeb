<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class DeleteTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Delete an existing track collection permanently. Provide either the collection ID or name to identify which collection to remove.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $name = $request->get('name');

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

        $collectionName = $collection->name;
        $trackCount = count($collection->tracks ?? []);

        // Delete the collection
        $collection->delete();

        $output = "✅ Successfully deleted collection: {$collectionName}\n";
        $output .= "Removed {$trackCount} tracks from the collection.\n";
        $output .= "The collection has been permanently removed from the database.\n";

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
                ->description('The collection ID to delete. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name to search for and delete. Either id or name must be provided.'),
        ];
    }
}
