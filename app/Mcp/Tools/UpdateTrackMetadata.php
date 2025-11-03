<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateTrackMetadata extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Update metadata (laps, gamemode, bots, etc.) for tracks in a collection. Can update all tracks or specific tracks by their IDs.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $collectionId = $request->get('id');
        $collectionName = $request->get('name');
        $trackIds = $request->get('track_ids', []);
        $metadata = $request->get('metadata', []);

        // Validate required parameters
        if (!$collectionId && !$collectionName) {
            return Response::text("Error: Either 'id' or 'name' parameter is required.");
        }

        if (empty($metadata)) {
            return Response::text("Error: No metadata provided. Please specify at least one metadata field to update.");
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

        // Get current tracks
        $tracks = $collection->tracks;
        $updatedCount = 0;
        $targetAll = empty($trackIds);

        // Validate metadata fields
        $validFields = ['gamemode', 'laps', 'bots', 'numTeams', 'carResetDisabled', 'wrongWayLimiterDisabled', 'carClassRestriction', 'carRestriction', 'weather'];
        $invalidFields = array_diff(array_keys($metadata), $validFields);

        if (!empty($invalidFields)) {
            return Response::text("Error: Invalid metadata fields: " . implode(', ', $invalidFields) . "\nValid fields: " . implode(', ', $validFields));
        }

        // Update tracks
        foreach ($tracks as $index => &$track) {
            $trackId = $track['track'] ?? null;

            // Skip if we're targeting specific tracks and this isn't one of them
            if (!$targetAll && !in_array($trackId, $trackIds)) {
                continue;
            }

            // Update metadata
            foreach ($metadata as $field => $value) {
                $track[$field] = $value;
            }

            $updatedCount++;
        }

        // Save the updated tracks
        $collection->tracks = $tracks;
        $collection->save();

        // Build response
        $targetDescription = $targetAll
            ? "all {$updatedCount} tracks"
            : "{$updatedCount} specified track(s)";

        $metadataDescription = [];
        foreach ($metadata as $field => $value) {
            $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $metadataDescription[] = "{$field}: {$valueStr}";
        }

        $output = "✓ Successfully updated {$targetDescription} in collection '{$collection->name}'\n\n";
        $output .= "Updated metadata:\n";
        foreach ($metadataDescription as $desc) {
            $output .= "  • {$desc}\n";
        }

        if (!$targetAll) {
            $output .= "\nUpdated tracks:\n";
            foreach ($tracks as $track) {
                if (in_array($track['track'], $trackIds)) {
                    $output .= "  • {$track['track']}\n";
                }
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
                ->description('The collection name to search for. Either id or name must be provided.'),
            'track_ids' => $schema->array()
                ->items($schema->string())
                ->description('Optional: Array of track IDs to update. If not provided, all tracks in the collection will be updated.'),
            'metadata' => $schema->object()
                ->properties([
                    'laps' => $schema->integer()->description('Number of laps for the race'),
                    'gamemode' => $schema->string()->description('Game mode (e.g., "racing", "derby")'),
                    'bots' => $schema->integer()->description('Number of AI bots'),
                    'numTeams' => $schema->integer()->description('Number of teams'),
                    'carResetDisabled' => $schema->boolean()->description('Whether car reset is disabled'),
                    'wrongWayLimiterDisabled' => $schema->boolean()->description('Whether wrong way limiter is disabled'),
                    'carClassRestriction' => $schema->string()->description('Car class restriction'),
                    'carRestriction' => $schema->string()->description('Specific car restriction'),
                    'weather' => $schema->string()->description('Weather setting'),
                ])
                ->description('Metadata fields to update. Provide only the fields you want to change.'),
        ];
    }
}
