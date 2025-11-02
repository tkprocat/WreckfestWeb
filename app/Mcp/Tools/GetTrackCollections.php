<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetTrackCollections extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Get track collections with their details. Can retrieve all collections or a specific one by ID or name. Returns collection name, tracks array, and metadata.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $name = $request->get('name');

        // Build query with filters
        $query = TrackCollection::query();

        if ($id) {
            $query->where('id', $id);
        }

        if ($name) {
            $query->where('name', $name);
        }

        $query->orderBy('updated_at', 'desc');

        // Get results
        $collections = $id || $name ? $query->get() : $query->get();

        // Handle not found for specific lookups
        if (($id || $name) && $collections->isEmpty()) {
            $identifier = $id ? "ID {$id}" : "name '{$name}'";

            return Response::text("❌ Error: Collection with {$identifier} not found");
        }

        // Handle empty results
        if ($collections->isEmpty()) {
            return Response::text('No track collections found.');
        }

        // Single collection - detailed view
        if (($id || $name) && $collections->count() === 1) {
            return Response::text($this->formatSingleCollection($collections->first()));
        }

        // Multiple collections - list view
        return Response::text($this->formatCollectionList($collections));
    }

    /**
     * Format a single collection with full details.
     */
    private function formatSingleCollection(TrackCollection $collection): string
    {
        $tracks = $collection->tracks ?? [];
        $output = "📋 Collection: {$collection->name}\n";
        $output .= "ID: {$collection->id}\n";
        $output .= 'Tracks: '.count($tracks)."\n";
        $output .= "Created: {$collection->created_at->diffForHumans()}\n";
        $output .= "Updated: {$collection->updated_at->diffForHumans()}\n\n";

        if (! empty($tracks)) {
            $output .= "Track IDs:\n";
            foreach ($tracks as $track) {
                $trackId = is_array($track) ? ($track['track'] ?? 'unknown') : $track;
                $output .= "  • {$trackId}\n";
            }
        }

        return $output;
    }

    /**
     * Format multiple collections as a list.
     */
    private function formatCollectionList($collections): string
    {
        $output = "Found {$collections->count()} track collections:\n\n";

        foreach ($collections as $collection) {
            $tracks = $collection->tracks ?? [];
            $output .= "📋 {$collection->name}\n";
            $output .= "   ID: {$collection->id} | Tracks: ".count($tracks)." | Updated: {$collection->updated_at->diffForHumans()}\n\n";
        }

        return $output;
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
                ->description('The collection ID to retrieve. If not provided, all collections will be returned.'),
            'name' => $schema->string()
                ->description('The collection name to search for. If not provided, all collections will be returned.'),
        ];
    }
}
